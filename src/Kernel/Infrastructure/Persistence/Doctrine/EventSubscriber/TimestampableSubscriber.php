<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Kernel\Application\Clock\ClockInterface;

/**
 * Gère automatiquement les timestamps sur les entités.
 * Utilise ClockInterface pour la testabilité.
 */
final class TimestampableSubscriber implements EventSubscriber
{
    private const CREATED_AT_FIELD = 'createdAt';
    private const UPDATED_AT_FIELD = 'updatedAt';

    public function __construct(
        private readonly ClockInterface $clock
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        $metadata = $args->getObjectManager()->getClassMetadata($entity::class);
        
        $now = $this->clock->now();

        if ($metadata->hasField(self::CREATED_AT_FIELD)) {
            $this->setFieldValue($entity, self::CREATED_AT_FIELD, $now);
        }

        if ($metadata->hasField(self::UPDATED_AT_FIELD)) {
            $this->setFieldValue($entity, self::UPDATED_AT_FIELD, $now);
        }
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();
        $metadata = $args->getObjectManager()->getClassMetadata($entity::class);

        if ($metadata->hasField(self::UPDATED_AT_FIELD)) {
            $now = $this->clock->now();
            $this->setFieldValue($entity, self::UPDATED_AT_FIELD, $now);
            
            // Informe Doctrine du changement
            $args->setNewValue(self::UPDATED_AT_FIELD, $now);
        }
    }

    private function setFieldValue(object $entity, string $field, mixed $value): void
    {
        $reflection = new \ReflectionClass($entity);
        
        if ($reflection->hasProperty($field)) {
            $property = $reflection->getProperty($field);
            $property->setAccessible(true);
            $property->setValue($entity, $value);
        }
    }
}