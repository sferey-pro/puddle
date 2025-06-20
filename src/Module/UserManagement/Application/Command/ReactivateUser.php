<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour réactiver un utilisateur.
 * Elle contient l'identifiant de l'utilisateur.
 */
final readonly class ReactivateUser implements CommandInterface
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}
