<?php

namespace App\Entity;

use Symfony\Component\Uid\Uuid;

interface EntityInterface
{
    public function getId(): ?int;

    public function getUuid(): ?Uuid;

    public function setUuid(Uuid $uuid): static;
}
