<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\ValueObject\Identifier;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour démarrer le Saga d'inscription.
 *
 * Rôle métier :
 * Cette commande représente l'intention de démarrer le "Parcours métier d'Inscription".
 * Elle embarque les données initiales et génère un ID unique qui sera utilisé
 * pour suivre ce parcours spécifique à travers toutes ses étapes.
 */
final readonly class StartRegistrationSaga implements CommandInterface
{
    private UserId $userId;

    public function __construct(
        public Identifier $identifier,
    ) {
        $this->userId = UserId::generate();
    }

    public function userId()
    {
        return $this->userId;
    }
}
