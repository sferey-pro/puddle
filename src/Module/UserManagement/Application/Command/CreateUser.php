<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour exécuter l'étape "Créer le profil utilisateur" du Saga.
 */
final readonly class CreateUser implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public EmailAddress $email,
    ) {
    }
}
