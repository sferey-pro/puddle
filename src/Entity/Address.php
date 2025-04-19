<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\Table(name: '`addresses`')]
class Address extends AbstractEntity
{
    #[ORM\OneToOne(targetEntity: Contact::class, mappedBy: 'address')]
    protected ?Contact $contact = null;

    #[ORM\Column(length: 255)]
    private string $city;

    public function jsonSerialize(): array
    {
        return [];
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }
}
