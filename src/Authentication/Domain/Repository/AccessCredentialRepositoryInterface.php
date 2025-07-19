<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\AccessCredential;
use Authentication\Domain\Model\Identity\CredentialId;
use Authentication\Domain\ValueObject\Token;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface AccessCredentialRepositoryInterface
{
    // ========== CRUD ==========
    public function save(AccessCredential $credential): void;
    public function remove(AccessCredential $credential): void;
}
