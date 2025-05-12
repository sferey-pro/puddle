<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Traits\BlameableEntity;
use App\Repository\SupplierRepository;
use App\Entity\ValueObject\SupplierId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
#[ORM\Table(name: '`suppliers`')]
class Supplier extends BaseEntity
{
    use BlameableEntity;

    public function __construct(
        #[ORM\Embedded(columnPrefix: false)]
        private readonly SupplierId $identifier,
        #[ORM\Column(length: 255)]
        private ?string $name = null,

    ) {

    }

    public function identifier(): SupplierId
    {
        return $this->identifier;
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
        ];
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
}
