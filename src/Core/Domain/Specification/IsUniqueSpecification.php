<?php

declare(strict_types=1);

namespace App\Core\Domain\Specification;

use App\Core\Domain\ValueObject\UniqueValueInterface;

/**
 * Spécification qui encapsule la règle métier "une valeur pour un champ donné est unique".
 */
final class IsUniqueSpecification extends AbstractSpecification
{
    private string $field;
    private mixed $value;

    public function __construct(
        private UniqueValueInterface $uniqueValueObject,
        private ?string $excludeId = null, // Optionnel: pour ignorer un ID lors des mises à jour
    ) {
        $this->field = $this->uniqueValueObject::uniqueFieldPath();
        $this->value = $this->uniqueValueObject->uniqueValue();
    }

    public function field(): string
    {
        return $this->field;
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function excludeId(): ?string
    {
        return $this->excludeId;
    }

    /**
     * Cette méthode est volontairement non implémentée car la logique de vérification
     * d'unicité ne peut pas s'appliquer à un seul objet candidat. Elle doit être
     * évaluée par un Repository qui a accès à l'ensemble de la collection.
     */
    public function isSatisfiedBy(mixed $candidate): bool
    {
        throw new \LogicException('IsUniqueSpecification cannot be checked in-memory. Use a SpecificationRepositoryInterface.');
    }
}
