<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SupplierRepository::class)]
#[ORM\Table(name: '`suppliers`')]
class Supplier extends AbstractEntity
{
    #[ORM\Column(length: 255)]
    private ?string $name = null;

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

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }
}
