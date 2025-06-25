<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel\Repository;

use App\Core\Domain\Repository\RepositoryInterface;
use App\Module\CostManagement\Application\ReadModel\RecurringCostView;
use App\Module\CostManagement\Domain\ValueObject\RecurringCostId;

/**
 * Interface pour le repository du Read Model RecurringCostView.
 *
 * Définit les méthodes nécessaires pour interagir avec la source de données
 * des vues de planifications de coûts récurrents.
 *
 * @template-extends RepositoryInterface<RecurringCostView>
 */
interface RecurringCostViewRepositoryInterface extends RepositoryInterface
{
    /**
     * Trouve une vue de poste de cout récurrent par son identifiant.
     *
     * @param RecurringCostId $id L'identifiant du poste de coût
     *
     * @return RecurringCostView|null la vue trouvée ou null
     */
    public function findById(RecurringCostId $id): ?RecurringCostView;

    /**
     * Sauvegarde (crée ou met à jour) une vue de coût récurrent.
     *
     * @param bool $flush si true, les changements sont immédiatement envoyés en base de données
     */
    public function save(RecurringCostView $recurringCost, bool $flush = false): void;

    /**
     * Récupère toutes les vues de coûts récurrents, triées par date de création.
     *
     * @return RecurringCostView[]
     */
    public function findAllOrderedByCreationDate(): array;

    /**
     * Trouve une entité CostItem par son ID ou lève une exception si elle n'est pas trouvée.
     *
     * @throws RecurringCostException
     */
    public function findOrFail(RecurringCostId $id): RecurringCostView;
}
