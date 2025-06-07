<?php

declare(strict_types=1);

namespace App\Core\Specification;

/**
 * Classe de base abstraite pour les implémentations concrètes de Specification.
 *
 * Fournit une base commune et encourage la composition en utilisant des méthodes
 * comme `and()`, `or()`, et `not()` pour créer des règles complexes.
 *
 * @template T Le type de l'objet candidat que cette spécification évalue.
 */
abstract class AbstractSpecification implements SpecificationInterface
{
    protected ?string $failureReason = null;

    /**
     * Vérifie si le candidat donné satisfait à la spécification.
     *
     * @param t $candidate L'objet à valider
     */
    abstract public function isSatisfiedBy(mixed $candidate): bool;

    public function getFailureReason(): ?string
    {
        return $this->failureReason;
    }

    protected function setFailureReason(string $reason): void
    {
        $this->failureReason = $reason;
    }
}
