<?php

declare(strict_types=1);

namespace App\Shared\Saga\Application;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Domain\ValueObject\AggregateRootId;

/**
 * Interface marqueur pour les commandes qui représentent une action au sein d'une Saga.
 * Elle garantit que la commande expose l'ID de corrélation (ici, le UserId)
 * pour que les mécanismes de la saga puissent retrouver l'état associé.
 */
interface SagaActionCommandInterface extends CommandInterface
{
    /**
     * Retourne l'identifiant de corrélation de la saga.
     */
    public function getCorrelationId(): AggregateRootId;
}
