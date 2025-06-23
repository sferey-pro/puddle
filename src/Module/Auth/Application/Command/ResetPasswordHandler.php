<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Orchestre le cas d'usage "Finaliser la réinitialisation du mot de passe".
 *
 * Ce handler contient la logique de validation du token et de mise à jour du mot de passe :
 * 1. Décortiquer le token public pour en extraire le sélecteur et le vérificateur.
 * 2. Retrouver la demande de réinitialisation originale via le sélecteur.
 * 3. Vérifier de manière sécurisée (HMAC) que le vérificateur est correct.
 * 4. Appeler la logique du domaine pour mettre à jour le mot de passe et invalider le token.
 */
#[AsCommandHandler]
final readonly class ResetPasswordHandler
{
    public function __construct(
        private PasswordResetRequestRepositoryInterface $passwordResetRequestRepository,
        private UserRepositoryInterface $userRepository,
        private PasswordHasherFactoryInterface $passwordHasherFactory,
        private ClockInterface $clock,
        private EventBusInterface $eventBus,
        private EntityManagerInterface $entityManager,
        private PasswordResetTokenGeneratorInterface $tokenGenerator,
    ) {
    }

    public function __invoke(ResetPassword $command): void
    {
        // Longueur en caractères du sélecteur (16 octets -> 32 hex)
        $selectorLength = 32;
        $selector = mb_substr($command->token, 0, $selectorLength);
        $verifier = mb_substr($command->token, $selectorLength);

        if (false === $selector || false === $verifier || '' === $verifier) {
            throw PasswordResetException::notFoundByToken();
        }

        $request = $this->passwordResetRequestRepository->ofSelector($selector);

        // Règle métier : la demande doit exister et ne pas être expirée.
        if (null === $request || $request->isExpired($this->clock->now())) {
            throw PasswordResetException::notFoundByToken();
        }

        // Règle de sécurité : on recrée la signature HMAC avec le vérificateur de l'URL
        // pour la comparer à celle stockée en base de données.
        $hashedTokenFromUrl = $this->tokenGenerator->createHashedToken(
            $request->userId(),
            $request->expiresAt(),
            $verifier
        );

        if (!hash_equals($request->hashedToken()->value, $hashedTokenFromUrl->value)) {
            throw PasswordResetException::notFoundByToken();
        }

        $userAccount = $this->userRepository->ofId($request->userId());
        if (null === $userAccount) {
            // Ce cas ne devrait logiquement pas arriver si l'intégrité de la BDD est maintenue.
            throw new \LogicException('User account not found for a valid password reset request.');
        }

        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher($userAccount);
        $hashedPassword = $passwordHasher->hash($command->newPassword);

        // On délègue à l'agrégat UserAccount la responsabilité de changer son propre mot de passe
        // et de marquer le token comme utilisé.
        $userAccount->resetPassword(
            $request,
            new Password($hashedPassword),
            $this->clock->now()
        );

        $this->entityManager->beginTransaction();
        try {
            // Sauvegarder les deux agrégats modifiés
            $this->passwordResetRequestRepository->save($request);
            $this->userRepository->save($userAccount);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        // Publier les événements
        $this->eventBus->publish(...array_merge($userAccount->pullDomainEvents(), $request->pullDomainEvents()));
    }
}
