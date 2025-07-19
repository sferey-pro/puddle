<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Adapter\In;

use Authentication\Domain\Service\AccountServiceInterface;

/**
 * Adaptateur IN pour le contexte Authentication
 * Traduit les appels du domaine Authentication vers le contexte Account
 */
final class AccountAdapter implements AccountServiceInterface, AccountVerificationServiceInterface
{
    public function __construct(
        private readonly AccountContextInterface $accountContext
    ) {}

    public function verifyAccountExists(string $userId): bool
    {
        try {
            $userIdVO = UserId::fromString($userId);
            return $this->accountContext->accountExists($userIdVO);
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function getAccountEmail(string $userId): ?string
    {
        $userIdVO = UserId::fromString($userId);
        $snapshot = $this->accountContext->findAccountSnapshot($userIdVO);

        return $snapshot?->email->toString();
    }

    public function canUserAuthenticate(string $userId): bool
    {
        try {
            $userIdVO = UserId::fromString($userId);
            return $this->accountContext->canAuthenticate($userIdVO);
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function hasExceededLoginAttempts(string $userId): bool
    {
        $userIdVO = UserId::fromString($userId);
        return $this->accountContext->hasReachedLoginLimit($userIdVO);
    }

    public function getAccountStatus(string $userId): ?array
    {
        $userIdVO = UserId::fromString($userId);
        $status = $this->accountContext->getAccountStatus($userIdVO);

        if ($status === null) {
            return null;
        }

        return [
            'isActive' => $status->isActive(),
            'isSuspended' => $status->isSuspended(),
            'suspensionReason' => $status->suspensionReason
        ];
    }
}
