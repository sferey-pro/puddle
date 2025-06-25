<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour désactiver un utilisateur.
 * Elle contient l'identifiant de l'utilisateur.
 */
final readonly class DeactivateUser implements CommandInterface
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}
