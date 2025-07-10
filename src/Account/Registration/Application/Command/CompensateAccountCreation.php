<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Kernel\Application\Message\CommandInterface;

/**
 * Commande pour compenser l'étape "Créer le compte".
 */
final readonly class CompensateAccountCreation implements CommandInterface
{
    public function __construct(
        public UserId $userId
    ) {
    }
}
