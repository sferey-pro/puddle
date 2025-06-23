<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Shared\Application\Command\CommandInterface;

final readonly class CleanupPasswordRequests implements CommandInterface
{
    /**
     * @param int $daysOld Le seuil en jours. Toutes les demandes expirées avant "aujourd'hui - X jours" seront supprimées.
     */
    public function __construct(
        public int $daysOld = 7,
    ) {
    }
}
