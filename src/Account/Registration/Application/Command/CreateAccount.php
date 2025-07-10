<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\CommandInterface;

/**
 * Commande pour l'étape "Créer le compte d'authentification".
 */
final class CreateAccount implements CommandInterface
{
    public function __construct(
        public UserId $userId
    ) {
    }
}
