<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Traits\BlameableEntity;
use App\Repository\CategoryRepository;
use App\Entity\ValueObject\CategoryId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: '`categories`')]
class Category extends BaseEntity
{
    use BlameableEntity;

    #[ORM\Embedded(columnPrefix: false)]
    private readonly CategoryId $identifier;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 7)]
    private ?string $color = null;

    #[ORM\Column(unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'category')]
    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function id(): CategoryId
    {
        return $this->identifier;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisplayName(): ?string
    {
        return ucfirst($this->name);
    }

    public function __toString(): string
    {
        return $this->getDisplayName();
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }
}
