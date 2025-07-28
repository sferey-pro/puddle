<?php

declare(strict_types=1);

namespace Authentication\Domain\Service;

/**
 * Port du contexte Authentication vers le contexte Account
 * Définit ce dont Authentication a besoin d'Account
 */
interface AccountServiceInterface
{
    public function verifyAccountExists(string $userId): bool;

    public function getAccountEmail(string $userId): ?string;
}
