<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ContactRepository;
use App\Entity\ValueObject\ContactId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: '`contacts`')]
class Contact extends BaseEntity
{
    #[ORM\Embedded(columnPrefix: false)]
    private readonly ContactId $identifier;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(nullable: true)]
    protected ?Category $category = null;

    #[ORM\OneToOne(targetEntity: Address::class, inversedBy: 'contact')]
    #[ORM\JoinColumn(nullable: false)]
    protected Address $address;

    #[ORM\Column(length: 255)]
    private string $name;

    public function id(): ContactId
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

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAddress(): Address
    {
        return $this->address;
    }

    public function setAddress(Address $address): static
    {
        $this->address = $address;

        return $this;
    }
}
