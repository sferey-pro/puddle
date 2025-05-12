<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class ContactId implements \Stringable
{
    use AggregateRootId;
}
