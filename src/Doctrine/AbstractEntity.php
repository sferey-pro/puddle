<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Doctrine\Entity\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class AbstractEntity implements EntityInterface, \JsonSerializable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    abstract public function jsonSerialize(): array;
}
