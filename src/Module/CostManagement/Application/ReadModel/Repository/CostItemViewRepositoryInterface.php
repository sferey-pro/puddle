<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel\Repository;

use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * Interface pour le repository du ReadModel CostItemView.
 *
 * Définit le contrat pour la récupération et la manipulation des objets CostItemView
 * dans la base de données de lecture (ex: MongoDB). Elle étend l'interface RepositoryInterface
 * pour les capacités de pagination et d'itération.
 *
 * @template-extends RepositoryInterface<CostItemView>
 */
interface CostItemViewRepositoryInterface extends RepositoryInterface
{
    /**
     * Trouve une vue de poste de coût par son identifiant.
     *
     * @param CostItemId $id L'identifiant du poste de coût
     *
     * @return CostItemView|null la vue trouvée ou null
     */
    public function findById(CostItemId $id): ?CostItemView;

    /**
     * Persiste une vue de poste de coût.
     * Gère à la fois la création (persist) et la mise à jour.
     *
     * @param CostItemView $costItem la vue à sauvegarder
     * @param bool         $flush    si true, les changements sont immédiatement envoyés en base de données
     */
    public function save(CostItemView $costItem, bool $flush = false): void;

    /**
     * Supprime une vue de poste de coût.
     *
     * @param CostItemView $costItem la vue à supprimer
     * @param bool         $flush    si true, la suppression est immédiatement envoyée en base de données
     */
    public function delete(CostItemView $costItem, bool $flush = false): void;

    /**
     * Trouve une entité CostItem par son ID ou lève une exception si elle n'est pas trouvée.
     *
     * @param CostItemId $id
     * @return CostItem
     * @throws CostItemException
     */
    public function findOrFail(CostItemId $id): CostItemView;
}
