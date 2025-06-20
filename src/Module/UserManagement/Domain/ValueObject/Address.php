<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\ValueObject;

final readonly class Address
{
    public string $street;
    public string $postalCode;
    public string $city;
    public string $country;

    public function __construct(string $street, string $postalCode, string $city, string $country)
    {
        $this->street = $street;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->country = $country;
    }

    /**
     * Retourne une représentation formatée pour l'affichage.
     */
    public function format(): string
    {
        return $this->street.' '.$this->postalCode.' '.$this->city.' '.$this->country;
    }
}
