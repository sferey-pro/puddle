<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence\Doctrine\EventSubscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PreRemoveEventArgs;
use Doctrine\ORM\Event\PostLoadEventArgs;
use Kernel\Application\Clock\ClockInterface;
use Kernel\Domain\Contract\Entity\Timestampable;
use Kernel\Domain\Contract\Entity\Blameable;
use Kernel\Domain\Contract\Entity\Versionable;
use Kernel\Domain\Contract\Entity\SoftDeletable;
use Kernel\Domain\Contract\Aggregate\EventSourced;
use Kernel\Domain\Contract\Aggregate\Snapshotable;
use Kernel\Domain\Contract\Service\Idempotent;
use Kernel\Application\Bus\EventBusInterface;
use Kernel\Application\Clock\SystemTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Subscriber Doctrine qui gère automatiquement tous les contracts/behaviors des entités.
 *
 * Responsabilités :
 * - Timestamps automatiques (Timestampable)
 * - Versioning automatique (Versionable)
 * - Audit trail (Blameable)
 * - Soft delete (SoftDeletable)
 * - Event sourcing (EventSourced)
 * - Snapshots (Snapshotable)
 * - Idempotence (Idempotent)
 */
final class ContractAwareSubscriber implements EventSubscriber
{
    public function __construct(
        private readonly ClockInterface $clock,
        private readonly EventBusInterface $eventBus,
        private readonly LoggerInterface $logger,
        private readonly ?Security $security = null, // Nullable pour les contextes CLI
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::preRemove,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
            Events::postLoad,
        ];
    }

    // ==================== PRE EVENTS ====================

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->handleTimestamps($entity, 'create');
        $this->handleBlameable($entity, 'create');
        $this->handleVersioning($entity, 'create');
        $this->handleSoftDeletable($entity, 'create');
        $this->handleIdempotence($entity, 'create');
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->handleTimestamps($entity, 'update');
        $this->handleBlameable($entity, 'update');
        $this->handleVersioning($entity, 'update');

        // Pour les updates, informer Doctrine des changements
        if ($entity instanceof Timestampable) {
            $args->setNewValue('updatedAt', $entity->getUpdatedAt());
        }

        if ($entity instanceof Versionable) {
            $args->setNewValue('version', $entity->getVersion());
        }
    }

    public function preRemove(PreRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        // Gérer le soft delete avant la suppression physique
        if ($entity instanceof SoftDeletable && !$entity->isDeleted()) {
            // Si l'entité supporte le soft delete et n'est pas encore soft deleted,
            // on peut empêcher la suppression physique
            $this->logger->warning('Attempting to hard delete a SoftDeletable entity', [
                'entity_class' => $entity::class,
                'entity_id' => method_exists($entity, 'getId') ? $entity->getId() : 'unknown'
            ]);
        }
    }

    // ==================== POST EVENTS ====================

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->handleEventSourcing($entity, 'persist');
        $this->handleSnapshot($entity, 'persist');
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->handleEventSourcing($entity, 'update');
        $this->handleSnapshot($entity, 'update');
    }

    public function postRemove(PostRemoveEventArgs $args): void
    {
        $entity = $args->getObject();

        $this->handleEventSourcing($entity, 'remove');
    }

    public function postLoad(PostLoadEventArgs $args): void
    {
        $entity = $args->getObject();

        // Peut être utilisé pour lazy loading ou initialisation
        if ($entity instanceof EventSourced) {
            // Reset des événements après chargement pour éviter les doublons
            $entity->markEventsAsCommitted();
        }
    }

    // ==================== HANDLERS PRIVÉS ====================

    private function handleTimestamps(object $entity, string $operation): void
    {
        if (!$entity instanceof Timestampable) {
            return;
        }

        $now = $this->clock->now();

        switch ($operation) {
            case 'create':
                if (method_exists($entity, 'setCreatedAt')) {
                    $entity->setCreatedAt($now);
                }
                if (method_exists($entity, 'setUpdatedAt')) {
                    $entity->setUpdatedAt($now);
                }
                break;

            case 'update':
                if (method_exists($entity, 'setUpdatedAt')) {
                    $entity->setUpdatedAt($now);
                }
                break;
        }
    }

    private function handleBlameable(object $entity, string $operation): void
    {
        if (!$entity instanceof Blameable || null === $this->security) {
            return;
        }
        /** @var UserSecurity|null $user */
        $user = $this->security->getUser();
        if (null === $user) {
            return; // Pas d'utilisateur connecté (CLI, job background, etc.)
        }

        // Assumons que l'user a une méthode getId() qui retourne un UserId
        $userId = method_exists($user, 'getId') ? $user->getId() : null;

        if (null === $userId) {
            return;
        }

        switch ($operation) {
            case 'create':
                if (method_exists($entity, 'setCreatedBy')) {
                    $entity->setCreatedBy($userId);
                }
                if (method_exists($entity, 'setUpdatedBy')) {
                    $entity->setUpdatedBy($userId);
                }
                break;

            case 'update':
                if (method_exists($entity, 'setUpdatedBy')) {
                    $entity->setUpdatedBy($userId);
                }
                break;
        }
    }

    private function handleVersioning(object $entity, string $operation): void
    {
        if (!$entity instanceof Versionable) {
            return;
        }

        switch ($operation) {
            case 'create':
                // La version commence à 1 pour les nouvelles entités
                if (method_exists($entity, 'setVersion')) {
                    $entity->setVersion(1);
                } else {
                    $entity->incrementVersion();
                }
                break;

            case 'update':
                // Incrémenter la version à chaque update
                $entity->incrementVersion();
                break;
        }
    }

    private function handleSoftDeletable(object $entity, string $operation): void
    {
        if (!$entity instanceof SoftDeletable) {
            return;
        }

        switch ($operation) {
            case 'create':
                // S'assurer que deletedAt est null pour les nouvelles entités
                if (method_exists($entity, 'markAsDeleted') && null === $entity->getDeletedAt()) {
                    $entity->setDeletedAt(null);
                }
                break;
        }
    }

    private function handleIdempotence(object $entity, string $operation): void
    {
        if (!$entity instanceof Idempotent) {
            return;
        }

        // Pour l'idempotence, on pourrait implémenter une vérification
        // de clé d'idempotence avant la persistance
        $idempotencyKey = $entity->getIdempotencyKey();

        $this->logger->info('Processing idempotent operation', [
            'entity_class' => $entity::class,
            'idempotency_key' => $idempotencyKey,
            'operation' => $operation,
            'ttl' => $entity->getIdempotencyTtl()
        ]);
    }

    private function handleEventSourcing(object $entity, string $operation): void
    {
        if (!$entity instanceof EventSourced) {
            return;
        }

        $events = $entity->getRecordedEvents();

        if (empty($events)) {
            return; // Pas d'événements à publier
        }

        try {
            // Publier tous les événements enregistrés
            $this->eventBus->publish(...$events);

            // Marquer les événements comme committed
            $entity->markEventsAsCommitted();

            $this->logger->debug('Published domain events for EventSourced entity', [
                'entity_class' => $entity::class,
                'events_count' => count($events),
                'operation' => $operation
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('Failed to publish domain events', [
                'entity_class' => $entity::class,
                'events_count' => count($events),
                'operation' => $operation,
                'error' => $e->getMessage()
            ]);

            // Rethrow pour que la transaction soit rollback
            throw $e;
        }
    }

    private function handleSnapshot(object $entity, string $operation): void
    {
        if (!$entity instanceof Snapshotable) {
            return;
        }

        // Logique de snapshot : par exemple, créer un snapshot tous les 10 événements
        if ($entity instanceof Versionable && $entity->getVersion() % 10 === 0) {
            try {
                $snapshot = $entity->toSnapshot();

                $this->logger->info('Created snapshot for Snapshotable entity', [
                    'entity_class' => $entity::class,
                    'version' => $entity->getSnapshotVersion(),
                    'operation' => $operation
                ]);

                // Ici, vous pourriez stocker le snapshot dans un store dédié
                // ou l'envoyer à un service externe

            } catch (\Throwable $e) {
                $this->logger->error('Failed to create snapshot', [
                    'entity_class' => $entity::class,
                    'operation' => $operation,
                    'error' => $e->getMessage()
                ]);

                // Ne pas faire échouer la transaction pour un snapshot raté
            }
        }
    }
}
