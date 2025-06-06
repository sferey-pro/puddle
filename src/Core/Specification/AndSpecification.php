<?php

declare(strict_types=1);

namespace App\Core\Specification;

/**
 * Représente une spécification composite de type "ET" (AND).
 *
 * Cette classe combine plusieurs spécifications et n'est satisfaite que si
 * TOUTES les spécifications qu'elle contient sont satisfaites par l'objet candidat.
 * Elle permet de chaîner des règles métier avec une logique d'intersection.
 *
 * @template T Le type de l'objet candidat que cette spécification évalue.
 * @implements SpecificationInterface<T>
 */
class AndSpecification implements SpecificationInterface
{
    /**
     * @var SpecificationInterface<T>[]
     */
    private array $specifications;

    /**
     * @param SpecificationInterface<T> ...$specifications Une liste de spécifications à combiner.
     */
    public function __construct(SpecificationInterface ...$specifications)
    {
        $this->specifications = $specifications;
    }

    /**
     * Vérifie si le candidat satisfait à TOUTES les spécifications.
     *
     * La méthode retourne `false` dès que la première spécification non satisfaite est trouvée.
     * Si toutes les spécifications sont satisfaites, elle retourne `true`.
     *
     * @param T $candidate L'objet à valider.
     * @return bool
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        foreach ($this->specifications as $specification) {
            if (!$specification->isSatisfiedBy($candidate)) {
                return false;
            }
        }

        return true;
    }
}
