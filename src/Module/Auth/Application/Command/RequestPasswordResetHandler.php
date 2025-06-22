<?php

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\PasswordResetRequest;
use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Orchestre la création d'une demande de réinitialisation de mot de passe.
 */
#[AsCommandHandler]
final readonly class RequestPasswordResetHandler
{
    private const MAX_RECENT_REQUESTS = 3;
    private const EXPIRES_AT_TIME = '+1 hour';

    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PasswordResetRequestRepositoryInterface $passwordResetRequestRepository,
        private PasswordResetTokenGeneratorInterface $tokenGenerator,
        private ClockInterface $clock,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RequestPasswordReset $command): void
    {
        $email = new Email($command->email);
        $ipAddress = new IpAddress($command->ipAddress);

        // 1. Contrôle du throttling par Email
        if ($this->passwordResetRequestRepository->countRecentRequests($email) >= self::MAX_RECENT_REQUESTS) {
            $nextAttemptAt = $this->passwordResetRequestRepository->findOldestNonExpiredRequestDate($email);
            throw PasswordResetException::throttling($nextAttemptAt ?? new \DateTimeImmutable('+1 hour'));
        }

        // 2. On cherche si l'utilisateur existe
        $userAccount = $this->userRepository->ofEmail($email);
        $expiresAt = $this->clock->now()->modify(self::EXPIRES_AT_TIME);

        if ($userAccount) {
            // 1. Générer un token (hashé pour le stockage, et en clair pour l'email)
            $tokenData = $this->tokenGenerator->generate($userAccount->id(), $expiresAt);

            // 2. Créer l'agrégat de demande de réinitialisation
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
            $request = PasswordResetRequest::logAttemptForUnknownUser($email, $ipAddress, $expiresAt);
        }

        // 3. Persister l'agrégat
        $this->passwordResetRequestRepository->save($request, true);

        // 4. Publier les événements de domaine
        $this->eventBus->publish(...$request->pullDomainEvents());

        return;
    }
}
