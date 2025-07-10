<?php

declare(strict_types=1);

namespace Identity\Application\Command;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\CommandInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Commande pour l'étape "Créer le compte d'authentification".
 */
final class CompensateIdentityAttachment implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public Identifier $identifier,
    ) {
    }
}
