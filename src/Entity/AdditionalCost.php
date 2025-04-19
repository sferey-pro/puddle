<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Traits\BlameableEntity;
use App\Repository\AdditionalCostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdditionalCostRepository::class)]
#[ORM\Table(name: '`additional_costs`')]
class AdditionalCost extends AbstractEntity
{
    use BlameableEntity;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $price = null;

    public function jsonSerialize(): array
    {
        return [];
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }
}
