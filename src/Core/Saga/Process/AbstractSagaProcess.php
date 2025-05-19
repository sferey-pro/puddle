<?php

declare(strict_types=1);

namespace App\Core\Saga\Process;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

/**
 * Classe de base pour les entités représentant l'état d'une Saga.
 * Chaque Saga concrète héritera de cette classe et aura sa propre table.
 */
#[ORM\MappedSuperclass]
abstract class AbstractSagaProcess
{
    use TimestampableEntity;

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true)]
        private(set) AbstractUid $id,

        #[ORM\Column(type: 'string', length: 255)]
        private(set) string $currentState,
    ) {
        $this->id = Uuid::v7();
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function currentState(): string
    {
        return $this->currentState;
    }

    abstract public static function getSagaTypeIdentifier(): string;
}
