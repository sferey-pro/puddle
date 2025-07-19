<?php

declare(strict_types=1);

namespace Account\Core\Application\Query;

use Account\Core\Domain\Model\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class GetAccountByIdHandler
{
    public function __construct(
        private AccountRepositoryInterface $accountRepository,
    ) {
    }

    public function __invoke(GetAccountById $query): ?Account
    {
        return $this->accountRepository->find($query->userId);
    }
}
