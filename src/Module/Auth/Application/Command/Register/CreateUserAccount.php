<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour l'étape "Créer le compte d'authentification".
 */
final class CreateUserAccount implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public EmailAddress $email,
    ) {
    }
}
