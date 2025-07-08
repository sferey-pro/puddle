<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\PasswordCredential;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Définit le contrat pour la persistance des agrégats PasswordCredential.
 * Fait partie du Domaine, ne connaît rien de Doctrine.
 */
interface PasswordCredentialRepositoryInterface
{
    public function ofUserId(UserId $id): ?PasswordCredential;

    public function save(PasswordCredential $credential): void;
}
