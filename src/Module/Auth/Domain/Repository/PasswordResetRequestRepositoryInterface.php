<?php

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\PasswordResetRequest;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\Email;

interface PasswordResetRequestRepositoryInterface
{
    public function save(PasswordResetRequest $request): void;

    public function ofId(PasswordResetRequestId $id): ?PasswordResetRequest;

    public function ofSelector(string $selector): ?PasswordResetRequest;

    /**
     * Compte le nombre de demandes de réinitialisation créées récemment pour un utilisateur.
     * "Récemment" (Non expiré) est défini par la durée de vie des tokens.
     */
    public function countRecentRequests(Email $email): int;

    /**
     * Trouve la date d'expiration la plus proche dans le futur pour un utilisateur donné.
     * Cela correspond à la demande la plus ancienne qui n'a pas encore expiré.
     */
    public function findOldestNonExpiredRequestDate(Email $email): ?\DateTimeImmutable;
}
