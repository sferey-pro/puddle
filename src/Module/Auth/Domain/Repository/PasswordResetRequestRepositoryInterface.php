<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\PasswordResetRequest;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\Email;

/**
 * Port de persistance pour les demandes de réinitialisation de mot de passe.
 */
interface PasswordResetRequestRepositoryInterface
{
    public function save(PasswordResetRequest $request): void;

    public function ofId(PasswordResetRequestId $id): ?PasswordResetRequest;

    public function ofSelector(string $selector): ?PasswordResetRequest;

    /**
     * Compte le nombre de demandes récentes (non expirées) pour un e-mail donné.
     * Utilisé par la logique de throttling pour prévenir le spam.
     *
     * @return int le nombre de demandes créées récemment
     */
    public function countRecentRequests(Email $email): int;

    /**
     * Trouve la date d'expiration la plus proche dans le futur pour un e-mail donné.
     * Utilisé par la logique de throttling pour informer l'utilisateur du temps d'attente.
     *
     * @return \DateTimeImmutable|null la date d'expiration la plus proche, ou null si aucune demande non expirée n'est trouvée
     */
    public function findOldestNonExpiredRequestDate(Email $email): ?\DateTimeImmutable;

    /**
     * Supprime toutes les demandes expirées avant une date seuil.
     * Utilisé par la tâche de nettoyage pour maintenir la base de données saine.
     *
     * @return int le nombre de demandes supprimées
     */
    public function deleteExpiredOlderThan(\DateTimeImmutable $threshold): int;
}
