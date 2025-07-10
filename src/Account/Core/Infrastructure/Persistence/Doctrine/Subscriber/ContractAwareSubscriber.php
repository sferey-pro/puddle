<?php

declare(strict_types=1);

namespace Account\Infrastructure\Doctrine\Subscriber;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Kernel\Domain\Contract\Entity\Timestampable;
use Kernel\Domain\Contract\Entity\Blameable;
use Kernel\Domain\Contract\Entity\Versionable;

final class ContractAwareSubscriber implements EventSubscriber
{
    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if ($entity instanceof Timestampable) {
            $this->handleTimestamps($entity);
        }

        if ($entity instanceof Blameable) {
            $this->handleBlameable($entity);
        }

        if ($entity instanceof Versionable) {
            $entity->incrementVersion();
        }
    }
}
