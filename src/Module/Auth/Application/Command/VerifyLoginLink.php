<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

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
