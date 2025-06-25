<?php

declare(strict_types=1);

namespace App\Core\Domain\Specification;

/**
 * Représente une spécification composite de type "OU" (OR).
 *
 * Cette classe combine plusieurs spécifications et est satisfaite si
 * AU MOINS UNE des spécifications qu'elle contient est satisfaite par l'objet candidat.
 * Elle permet de chaîner des règles métier avec une logique d'union.
 *
 * @template T Le type de l'objet candidat que cette spécification évalue.
 *
 * @implements SpecificationInterface<T>
 */
class OrSpecification extends AbstractSpecification implements SpecificationInterface
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
     * Vérifie si le candidat satisfait à AU MOINS UNE des spécifications.
     *
     * La méthode retourne `true` dès que la première spécification satisfaite est trouvée.
     * Si aucune spécification n'est satisfaite, elle retourne `false`.
     *
     * @param t $candidate L'objet à valider
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        foreach ($this->specifications as $specification) {
            if ($specification->isSatisfiedBy($candidate)) {
                return true;
            }
        }

        return false;
    }
}
