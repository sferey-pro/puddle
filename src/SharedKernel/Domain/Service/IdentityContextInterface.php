<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;

use SharedKernel\Domain\DTO\Identity\UserIdentifiersDTO;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;

interface IdentityContextInterface
{
    public function getUserIdentifiers(UserId $accountId): ?UserIdentifiersDTO;

    public function identifierExists(string $type, string $value): bool;

    public function emailExists(EmailAddress $email): bool;
}
