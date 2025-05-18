<?php

declare(strict_types=1);

namespace App\Core\Specification;

readonly class NotSpecification implements SpecificationInterface
{
    /**
     * @param Specification $specification
     */
    public function __construct(private SpecificationInterface $specification)
    {
    }

    public function isSatisfiedBy(mixed $candidate): bool
    {
        return !$this->specification->isSatisfiedBy($candidate);
    }
}
