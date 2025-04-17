<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;

abstract class AbstractEntity implements EntityInterface, \JsonSerializable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(type: UuidType::NAME)]
    protected ?Uuid $uuid = null;

    public function __construct()
    {
        $this->uuid = Uuid::v7();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    abstract public function jsonSerialize(): array;
}
