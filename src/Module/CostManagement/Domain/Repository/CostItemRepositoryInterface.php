<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Repository;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Shared\Domain\Repository\RepositoryInterface;

/**
 * Interface pour le repository de l'agrégat CostItem.
 *
 * Définit le contrat pour la persistance et la récupération des postes de coûts.
 * Cette abstraction permet au domaine de rester indépendant des détails de
 * l'implémentation de la persistance (ex: Doctrine ORM, MongoDB).
 *
 * @extends RepositoryInterface<CostItem>
 */
interface CostItemRepositoryInterface extends RepositoryInterface
{
    /**
     * Persiste un agrégat CostItem.
     * Si $flush est vrai, les changements sont immédiatement envoyés à la base de données.
     */
    public function save(CostItem $model, bool $flush = false): void;

    /**
     * Ajoute un agrégat CostItem à l'unité de travail pour la persistance.
     */
    public function add(CostItem $model): void;

    /**
     * Marque un agrégat CostItem pour suppression.
     */
    public function remove(CostItem $model): void;

    /**
     * Recherche un CostItem par son identifiant unique.
     *
     * @return CostItem|null L'agrégat trouvé ou null s'il n'existe pas
     */
    public function ofId(CostItemId $id): ?CostItem;

    /**
     * Trouve tous les postes de coûts qui sont actuellement actifs et non entièrement couverts.
     * Utile pour des processus métier comme l'allocation de revenus.
     *
     * @return CostItem[]
     */
    public function findActiveAndUncovered(): array;

    /**
     * Trouve une entité CostItem par son ID ou lève une exception si elle n'est pas trouvée.
     *
     * @throws CostItemException
     */
    public function findOrFail(CostItemId $id): CostItem;

    /**
     * Trouve tous les postes de coûts.
     *
     * @return CostItem[]
     */
    public function findAll(): array;
}
