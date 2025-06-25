<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour créer un UserAccount suite à la création d'un User dans un autre contexte.
 */
final readonly class CreateAssociatedUserAccount implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Email $email,
    ) {
    }
}
