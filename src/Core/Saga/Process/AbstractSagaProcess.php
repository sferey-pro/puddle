<?php

declare(strict_types=1);

namespace App\Core\Saga\Process;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Uid\Uuid;

/**
 * Classe de base pour les entités représentant l'état d'une Saga.
 * Utilise l'héritage Doctrine MAPPED_SUPERCLASS.
 * Chaque Saga concrète héritera de cette classe et aura sa propre table.
 */
#[ORM\MappedSuperclass]
abstract class AbstractSagaProcess
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    protected Uuid $id;

    #[ORM\Column(type: 'string', length: 255)]
    protected string $currentState;

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState): void
    {
        $this->currentState = $currentState;
    }

    abstract public static function getSagaTypeIdentifier(): string;
}
