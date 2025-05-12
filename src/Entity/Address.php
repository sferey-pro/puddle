<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AddressRepository;
use App\Entity\ValueObject\AddressId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\Table(name: '`addresses`')]
class Address extends BaseEntity
{
    #[ORM\Embedded(columnPrefix: false)]
    private readonly AddressId $identifier;

    #[ORM\OneToOne(targetEntity: Contact::class, mappedBy: 'address')]
    protected ?Contact $contact = null;

    #[ORM\Column(length: 255)]
    private string $city;

    public function id(): AddressId
    {
        return $this->identifier;
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
