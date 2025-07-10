<?php

namespace Account\Core\Domain\Repository;

use Account\Core\Domain\Account;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Kernel\Domain\Specification\SpecificationInterface;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;

interface AccountRepositoryInterface
{
    public function remove(Account $account): void;

    public function ofId(UserId $id): ?Account;

    public function ofEmail(EmailAddress $email): ?Account;

    public function ofPhone(PhoneNumber $phone): ?Account;

    /**
     * @return Account[]
     */
    public function matching(SpecificationInterface $specification): array;

    public function count(SpecificationInterface $specification): int;
}
