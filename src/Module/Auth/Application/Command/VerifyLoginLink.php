<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour marquer un lien de connexion comme utilisé après un succès de connexion.
 */
final readonly class VerifyLoginLink implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Hash $hash,
    ) {
    }
}
