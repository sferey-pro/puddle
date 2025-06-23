<?php

namespace App\Module\Auth\Application\Command;

use App\Shared\Application\Command\CommandInterface;

/**
 * Commande pour déclencher le nettoyage des anciennes demandes de réinitialisation de mot de passe.
 */
final readonly class CleanupPasswordRequests implements CommandInterface
{
    /**
     * @param int $daysOld Supprime les demandes expirées depuis plus de X jours.
     */
    public function __construct(
        public int $daysOld = 7
    ) {
    }
}
