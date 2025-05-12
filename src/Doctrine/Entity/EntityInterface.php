<?php

declare(strict_types=1);

namespace App\Doctrine\Entity;

interface EntityInterface
{
    public function getId(): ?int;
}
