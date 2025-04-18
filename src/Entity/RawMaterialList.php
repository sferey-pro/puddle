<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RawMaterialListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RawMaterialListRepository::class)]
#[ORM\Table(name: '`raw_material_lists`')]
class RawMaterialList extends AbstractEntity
{
    private ?Product $product = null;

    #[ORM\OneToMany(mappedBy: 'rawMaterialList', targetEntity: RawMaterialItem::class, orphanRemoval: true, cascade: ['persist'])]
    private Collection $rawMaterialItems;

    public function __construct()
    {
        $this->rawMaterialItems = new ArrayCollection();
    }

    public function jsonSerialize(): array
    {
        return [];
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    /**
     * @return Collection<int, RawMaterialItem>
     */
    public function getRawMaterialItems(): Collection
    {
        return $this->rawMaterialItems;
    }

    public function addRawMaterialItem(RawMaterialItem $rawMaterialItem): self
    {
        if (!$this->rawMaterialItems->contains($rawMaterialItem)) {
            $this->rawMaterialItems[] = $rawMaterialItem;
            $rawMaterialItem->setRawMaterialList($this);
        }

        return $this;
    }

    public function removeRawMaterialItem(RawMaterialItem $rawMaterialItem): self
    {
        if ($this->rawMaterialItems->removeElement($rawMaterialItem)) {
            if ($rawMaterialItem->getRawMaterialList() === $this) {
                $rawMaterialItem->setRawMaterialList(null);
            }
        }

        return $this;
    }
}
