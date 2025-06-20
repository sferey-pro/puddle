<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\DTO;

/**
 * DTO pour la mise à jour des informations d'un utilisateur.
 * Contient les données nécessaires pour mettre à jour un profil utilisateur.
 */
final readonly class UpdateUserDTO
{
    public ?string $username;
}
