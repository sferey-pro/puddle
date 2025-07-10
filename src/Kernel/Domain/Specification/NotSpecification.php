<?php

declare(strict_types=1);

namespace Kernel\Domain\Specification;

/**
 * Représente une spécification décoratrice de type "NON" (NOT).
 *
 * Cette classe encapsule une autre spécification et inverse son résultat.
 * Elle est satisfaite si la spécification qu'elle contient n'est PAS satisfaite, et vice-versa.
 *
 * @template T Le type de l'objet candidat que cette spécification évalue.
 *
 * @implements SpecificationInterface<T>
 */
class NotSpecification extends AbstractSpecification implements SpecificationInterface
{
    /**
     * @param SpecificationInterface<T> $specification la spécification à inverser
     */
    public function __construct(private SpecificationInterface $specification)
    {
    }

    /**
     * Vérifie si le candidat NE satisfait PAS à la spécification encapsulée.
     *
     * @param t $candidate L'objet à valider
     *
     * @return bool le résultat inversé de la spécification décorée
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        return !$this->specification->isSatisfiedBy($candidate);
    }
}
