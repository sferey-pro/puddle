<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Specification;

use Doctrine\ORM\QueryBuilder;
use Kernel\Domain\Specification\SpecificationInterface;

/**
 * Adapte les Specifications du domaine pour générer des requêtes Doctrine.
 * Permet d'utiliser le pattern Specification avec l'ORM.
 */
abstract class DoctrineSpecificationAdapter implements SpecificationInterface
{
    /**
     * Modifie le QueryBuilder selon les critères de la Specification.
     */
    abstract public function modifyQuery(QueryBuilder $qb, string $alias): void;

    /**
     * Alias/paramètres à utiliser pour éviter les collisions.
     */
    abstract public function getParameterName(): string;
}