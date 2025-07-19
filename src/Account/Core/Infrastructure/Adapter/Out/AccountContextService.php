<?php

declare(strict_types=1);

namespace Account\Core\Infrastructure\Adapter\Out;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Lifecycle\Domain\State\ActiveState;
use Account\Lifecycle\Domain\State\SuspendedState;
use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use SharedKernel\Domain\Service\AccountContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\DTO\Account\AccountStatusDTO;

final class AccountContextService implements AccountContextInterface
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_ATTEMPT_WINDOW_MINUTES = 30;

    public function __construct(
        private readonly AccountRepositoryInterface $accountRepository,
        private readonly LoginAttemptRepositoryInterface $loginAttemptRepository
    ) {}

    public function getAccountStatus(UserId $userId): ?AccountStatusDTO
    {
        /** @var Account $account */
        $account = $this->accountRepository->find($userId);

        if ($account === null) {
            return null;
        }

        return new AccountStatusDTO(
            id: $account->id,
            state: $account->getState(),
            createdAt: $account->getCreatedAt()
        );
    }

    public function accountExists(UserId $userId): bool
    {
        return $this->accountRepository->exists($userId);
    }

    public function canAuthenticate(UserId $userId): bool
    {
        $state = $this->getAccountStatus($userId);

        if ($state === null) {
            return false;
        }

        // Vérifier si le compte est actif
        if (!$state instanceof ActiveState) {
            return false;
        }

        // Vérifier si la suspension temporaire est expirée
        if ($state instanceof SuspendedState) {
            return false;
        }

        // Vérifier les tentatives de connexion
        if ($this->hasReachedLoginLimit($userId)) {
            return false;
        }

        return true;
    }

    public function hasReachedLoginLimit(UserId $userId): bool
    {
        $recentAttempts = $this->loginAttemptRepository->countFailedAttemptsByUser(
            $userId,
            new \DateTimeImmutable("-" . self::LOGIN_ATTEMPT_WINDOW_MINUTES . " minutes")
        );

        return $recentAttempts >= self::MAX_LOGIN_ATTEMPTS;
    }
}
