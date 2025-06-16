<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\DTO\ChangeEmailDTO;
use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour mettre à jour le profil d'un utilisateur.
 * Elle contient l'identifiant de l'utilisateur et les données de mise à jour.
 */
final readonly class ChangeUserEmail implements CommandInterface
{
    public function __construct(
        public UserId $userId,
        public ChangeEmailDTO $dto,
    ) {
    }
}
