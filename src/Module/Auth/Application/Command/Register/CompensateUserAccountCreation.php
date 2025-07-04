<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Core\Application\Command\CommandInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour compenser l'étape "Créer le compte d'authentification".
 */
final readonly class CompensateUserAccountCreation implements CommandInterface
{
    public function __construct(public UserId $userId)
    {
    }
}
