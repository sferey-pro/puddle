<?php

declare(strict_types=1);

namespace Kernel\Domain\Entity;

/**
 * Base pour toutes les entités du domaine.
 * Une entité a une identité qui persiste dans le temps.
 */
abstract class Entity
{
    /**
     * Compare deux entités par leur identité.
     */
    public function equals(self $other): bool
    {
        return $this->getId()->equals($other->getId());
    }

    /**
     * Retourne l'identifiant unique de l'entité.
     */
    abstract public function getId(): EntityId;

    /**
     * Méthode magique pour empêcher la sérialisation.
     * Force l'utilisation explicite de méthodes de sérialisation.
     */
    public function __sleep(): array
    {
        throw new \BadMethodCallException('Entities should not be serialized directly');
    }

    /**
     * Clone une entité en préservant son identité.
     */
    public function __clone()
    {
        // L'identité doit rester la même après clonage
    }
}