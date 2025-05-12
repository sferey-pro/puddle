<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\ValueObject\RawMaterialItemId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: '`raw_material_items`')]
class RawMaterialItem extends BaseEntity
{
    #[ORM\Embedded(columnPrefix: false)]
    private readonly RawMaterialItemId $identifier;

    #[ORM\Column]
    private ?RawMaterial $rawMaterial = null;

    #[ORM\Column]
    private ?float $quantity = null;

    #[ORM\Column(length: 255)]
    private ?string $unit = null;

    #[ORM\ManyToOne(inversedBy: 'rawMaterialItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RawMaterialList $rawMaterialList;

    public function id(): RawMaterialItemId
    {
        return $this->identifier;
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
