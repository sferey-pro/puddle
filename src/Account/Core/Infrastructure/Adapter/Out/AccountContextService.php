<?php

declare(strict_types=1);

namespace Account\Core\Infrastructure\Adapter\Out;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Lifecycle\Domain\Model\State\ActiveState;
use Account\Lifecycle\Domain\Model\State\SuspendedState;
use SharedKernel\Domain\Service\AccountContextInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\DTO\Account\AccountStatusDTO;
use SharedKernel\Domain\Service\AuthenticationContextInterface;

final readonly class AccountContextService implements AccountContextInterface
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_ATTEMPT_WINDOW_MINUTES = 30;

    public function __construct(
        private AccountRepositoryInterface $accountRepository,
        private AuthenticationContextInterface $authContext,
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
        // Déléguer au contexte Authentication via SharedKernel
        $authHistory = $this->authContext->getAuthenticationHistory($userId, 10);

        $recentFailures = $authHistory->getRecentFailureCount(self::LOGIN_ATTEMPT_WINDOW_MINUTES);
        return $recentFailures >= self::MAX_LOGIN_ATTEMPTS;
    }
}
