<?php

declare(strict_types=1);

namespace App\Entity\ValueObject;

use App\Doctrine\Entity\IdentifierInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class UserId implements IdentifierInterface, \Stringable
{
    use AggregateRootId;
}
