<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;

use Kernel\Domain\Result;
use SharedKernel\Domain\DTO\Identity\UserIdentifiersDTO;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface IdentityContextInterface
{
    public function getUserIdentifiers(UserId $userId): ?UserIdentifiersDTO;
    public function findUserIdByIdentifier(string $identifierValue): ?UserId;

    public function resolveIdentifier(string $value): ?Result;
}
