<?php

declare(strict_types=1);

namespace Account\Core\Domain\Repository;

use Account\Core\Domain\Model\UserProfile;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface UserProfileRepositoryInterface
{
    /**
     * Trouve un profil par son ID utilisateur.
     */
    public function findById(UserId $userId): ?UserProfile;

    /**
     * Vérifie si un profil existe.
     */
    public function exists(UserId $userId): bool;

    /**
     * Persiste un profil.
     */
    public function save(UserProfile $profile): void;

    /**
     * Supprime un profil.
     */
    public function remove(UserProfile $profile): void;

    /**
     * Recherche des profils par nom d'affichage.
     *
     * @return UserProfile[]
     */
    public function findByDisplayNameLike(string $search, int $limit = 10): array;
}
