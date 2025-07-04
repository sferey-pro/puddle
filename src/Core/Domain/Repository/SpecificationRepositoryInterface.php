<?php

declare(strict_types=1);

namespace App\Core\Domain\Repository;

use App\Core\Domain\Specification\SpecificationInterface;

/**
 * Contrat pour les repositories qui peuvent évaluer des Spécifications,
 * notamment pour compter le nombre d'entités qui les satisfont.
 */
interface SpecificationRepositoryInterface
{
    /**
     * Compte le nombre d'entités qui satisfont une spécification donnée.
     */
    public function countBySpecification(SpecificationInterface $specification): int;
}
