<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Kernel\Application\Message\CommandInterface;

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
    private(set) UserId $userId;

    public function __construct(
        public string $identifier,
        public ?string $ipAddress = null
    ) {
        $this->userId = UserId::generate();
    }
}
