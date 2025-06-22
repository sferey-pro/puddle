<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\HashedToken;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Orchestre la finalisation de la réinitialisation du mot de passe.
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
    ) {
    }

    public function __invoke(ResetPassword $command): void
    {
        $selector = mb_substr($command->token, 0, 32);
        $verifier = mb_substr($command->token, 32);

        $hashedToken = new HashedToken($command->token);

        $request = $this->passwordResetRequestRepository->ofSelector($selector);

        if (null === $request) {
            throw PasswordResetException::notFoundByToken();
        }

        $userAccount = $this->userRepository->ofId($request->userId());
        if (null === $userAccount) {
            // Devrait être impossible si la BDD est cohérente, mais c'est une bonne pratique.
            throw new \LogicException('User account not found for a valid password reset request.');
        }

        // Hashage du nouveau mot de passe
        $passwordHasher = $this->passwordHasherFactory->getPasswordHasher($userAccount);
        $hashedPassword = $passwordHasher->hash($command->newPassword);

        // Appel de la méthode du domaine qui contient les règles métier
        $userAccount->resetPassword(
            $request,
            new Password($hashedPassword),
            $this->clock->now()
        );

        $this->entityManager->beginTransaction();
        try {
            // Sauvegarder les deux agrégats modifiés
            $this->passwordResetRequestRepository->save($request);
            $this->userRepository->save($userAccount, true);

            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable $e) {
            // En cas d'erreur, on annule tout
            $this->entityManager->rollback();
            throw $e; // On relance l'exception
        }

        // Publier les événements
        $this->eventBus->publish(...array_merge($userAccount->pullDomainEvents(), $request->pullDomainEvents()));
    }
}
