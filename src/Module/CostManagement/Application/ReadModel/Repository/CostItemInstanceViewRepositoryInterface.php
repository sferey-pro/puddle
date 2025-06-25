<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\ReadModel\Repository;

use App\Core\Domain\Repository\RepositoryInterface;
use App\Module\CostManagement\Application\ReadModel\CostItemInstanceView;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;

/**
 * Interface pour le repository du ReadModel CostItemInstanceView.
 *
 * Définit le contrat pour la récupération et la manipulation des objets CostItemInstanceView
 * dans la base de données de lecture (ex: MongoDB). Elle étend l'interface RepositoryInterface
 * pour les capacités de pagination et d'itération.
 *
 * @template-extends RepositoryInterface<CostItemInstanceView>
 */
interface CostItemInstanceViewRepositoryInterface extends RepositoryInterface
{
    /**
     * Trouve une vue de poste de coût par son identifiant.
     *
     * @param CostItemId $id L'identifiant du poste de coût
     *
     * @return CostItemInstanceView|null la vue trouvée ou null
     */
    public function findById(CostItemId $id): ?CostItemInstanceView;

    /**
     * Persiste une vue de poste de coût.
     * Gère à la fois la création (persist) et la mise à jour.
     *
     * @param CostItemInstanceView $costItem la vue à sauvegarder
     * @param bool                 $flush    si true, les changements sont immédiatement envoyés en base de données
     */
    public function save(CostItemInstanceView $costItem, bool $flush = false): void;

    /**
     * Supprime une vue de poste de coût.
     *
     * @param CostItemInstanceView $costItem la vue à supprimer
     * @param bool                 $flush    si true, la suppression est immédiatement envoyée en base de données
     */
    public function delete(CostItemInstanceView $costItem, bool $flush = false): void;

    /**
     * Trouve une entité CostItemInstanceView par son ID ou lève une exception si elle n'est pas trouvée.
     *
     * @throws CostItemException
     */
    public function findOrFail(CostItemId $id): CostItemInstanceView;
}
