<?php

declare(strict_types=1);

namespace Identity\Domain\Repository;

use Identity\Domain\Model\UserIdentity;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface UserIdentityRepositoryInterface
{
    // ========== CRUD ==========
    public function save(UserIdentity $userIdentity): void;
    public function remove(UserIdentity $userIdentity): void;


    // ========== SEARCH ==========
    public function findByUserId(UserId $userId): ?UserIdentity;
    public function findByIdentifierValue(string $value): ?UserIdentity;
    public function findByTypedIdentifier(string $type, string $value): ?UserIdentity;


    // ========== QUERY OPTIMIZATION ==========
    public function findUserIdByIdentifier(string $value): ?UserId;
}
