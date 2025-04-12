<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class RawMaterialItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?RawMaterial $rawMaterial = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $unit = null;

    #[ORM\ManyToOne(inversedBy: 'rawMaterialItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RawMaterialList $rawMaterialList;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRawMaterial(): ?RawMaterial
    {
        return $this->rawMaterial;
    }

    public function setRawMaterial(?RawMaterial $rawMaterial): static
    {
        $this->rawMaterial = $rawMaterial;

        return $this;
    }

    public function getQuantity(): ?float
    {
        return $this->quantity;
    }

    public function setQuantity(float $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): static
    {
        $this->unit = $unit;

        return $this;
    }

    public function getRawMaterialList(): ?RawMaterialList
    {
        return $this->rawMaterialList;
    }

    public function setRawMaterialList(?RawMaterialList $rawMaterialList): self
    {
        $this->rawMaterialList = $rawMaterialList;

        return $this;
    }
}
