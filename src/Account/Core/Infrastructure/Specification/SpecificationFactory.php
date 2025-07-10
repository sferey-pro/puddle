<?php

namespace Account\Core\Infrastructure\Specification;

use Account\Core\Domain\Specification\ActiveAccountSpecification;
use Account\Infrastructure\Specification\DoctrineCreatedAfterSpecification;
use Kernel\Domain\Specification\AndSpecification;
use Kernel\Domain\Specification\SpecificationInterface;
use Kernel\Infrastructure\Specification\DoctrineAndSpecification;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;

final class SpecificationFactory
{
    /**
     * Convertit une Specification du domaine en version Doctrine.
     */
    public function createDoctrineSpecification(
        SpecificationInterface $specification
    ): DoctrineSpecificationAdapter {
        return match ($specification::class) {
            ActiveAccountSpecification::class => new DoctrineActiveAccountSpecification($specification),
            DoctrineCreatedAfterSpecification::class => new DoctrineCreatedAfterSpecification($specification->getDate()),
            // Gestion de la composition
            AndSpecification::class => new DoctrineAndSpecification(
                $this->createDoctrineSpecification($specification->getLeft()),
                $this->createDoctrineSpecification($specification->getRight())
            ),
            default => throw new \InvalidArgumentException('Unsupported specification type')
        };
    }
}
