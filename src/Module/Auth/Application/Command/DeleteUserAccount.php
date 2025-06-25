<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour supprimer un UserAccount.
 */
final readonly class DeleteUserAccount implements CommandInterface
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}
