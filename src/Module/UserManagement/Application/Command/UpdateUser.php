<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\DTO\UpdateUserDTO;

/**
 * Commande pour mettre à jour le profil d'un utilisateur.
 * Elle contient l'identifiant de l'utilisateur et les données de mise à jour.
 */
final readonly class UpdateUser implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public UpdateUserDTO $dto,
    ) {
    }
}
