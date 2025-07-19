<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\AccessCredential\AbstractAccessCredential;
use Authentication\Domain\ValueObject\Token;

interface AccessCredentialRepositoryInterface
{
    // ========== CRUD ==========
    public function save(AbstractAccessCredential $credential): void;
    public function remove(AbstractAccessCredential $credential): void;


    // ========== QUERY ==========
    public function findByToken(Token $token): ?AbstractAccessCredential;

    /**
     * Nettoie les credentials expirés.
     */
    public function removeExpired(): int;
}
