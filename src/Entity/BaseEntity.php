<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\AbstractEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;

class BaseEntity extends AbstractEntity
{
    use TimestampableEntity;

    public function jsonSerialize(): array
    {
        return [];
    }
}
