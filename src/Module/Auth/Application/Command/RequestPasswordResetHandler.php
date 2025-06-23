<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\PasswordResetRequest;
use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

/**
 * Orchestre le cas d'usage "Demander une réinitialisation de mot de passe".
 *
 * Ce handler contient la logique applicative :
 * 1. Appliquer les règles de sécurité (throttling) pour prévenir les abus.
 * 2. Déterminer si la demande concerne un utilisateur connu ou inconnu.
 * 3. Créer une trace de la tentative dans tous les cas (pour l'audit).
 * 4. Pour un utilisateur connu, générer un token sécurisé et déclencher l'envoi de l'e-mail.
 */
#[AsCommandHandler]
final readonly class RequestPasswordResetHandler
{
    // Limite le nombre de demandes par heure pour un même e-mail pour éviter le spam.
    private const MAX_RECENT_REQUESTS = 3;

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRequestRepositoryInterface $passwordResetRequestRepository,
        private PasswordResetTokenGeneratorInterface $tokenGenerator,
        private ClockInterface $clock,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RequestPasswordReset $command): \DateTimeImmutable
    {
        $email = new Email($command->email);
        $ipAddress = new IpAddress($command->ipAddress);

        // Règle de sécurité : si la même adresse e-mail a fait trop de demandes récentes,
        // on lève une exception pour bloquer la tentative et informer l'utilisateur.
        if ($this->passwordResetRequestRepository->countRecentRequests($email) >= self::MAX_RECENT_REQUESTS) {
            $nextAttemptAt = $this->passwordResetRequestRepository->findOldestNonExpiredRequestDate($email);
            throw PasswordResetException::throttling($nextAttemptAt ?? new \DateTimeImmutable('+1 hour'));
        }

        $userAccount = $this->userRepository->ofEmail($email);
        $expiresAt = $this->clock->now()->modify($command::EXPIRES_AT_TIME);

        if ($userAccount) {
            // Cas d'un utilisateur connu : on génère une demande complète et sécurisée.
            $tokenData = $this->tokenGenerator->generate($userAccount->id(), $expiresAt);
            $request = PasswordResetRequest::createForRealUser(
                $userAccount->id(),
                $email,
                $ipAddress,
                $expiresAt,
                $tokenData['selector'],
                $tokenData['hashedToken'],
                $tokenData['publicToken'],
            );
        } else {
            // Cas d'un utilisateur inconnu : on logue la tentative à des fins de sécurité
            // sans générer de token ni envoyer d'e-mail.
            $request = PasswordResetRequest::logAttemptForUnknownUser($email, $ipAddress, $expiresAt);
        }

        $this->passwordResetRequestRepository->save($request, true);
        $this->eventBus->publish(...$request->pullDomainEvents());

        return $expiresAt;
    }
}
