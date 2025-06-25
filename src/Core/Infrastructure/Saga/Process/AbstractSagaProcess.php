<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Saga\Process;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

/**
 * Classe de base pour les entités représentant l'état d'une Saga.
 * Chaque Saga concrète héritera de cette classe et aura sa propre table.
 */
abstract class AbstractSagaProcess
{
    use TimestampableEntity;

    public function __construct(
        private(set) AbstractUid $id,
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

    abstract public static function getSagaTypeid(): string;
}
