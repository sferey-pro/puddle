<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour compenser l'étape "Créer le profil utilisateur".
 */
final readonly class CompensateUserCreation implements CommandInterface
{
    public function __construct(public UserId $userId)
    {
    }
}
