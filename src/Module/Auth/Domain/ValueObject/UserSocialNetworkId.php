<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Shared\Domain\ValueObject\AggregateRootId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final class UserSocialNetworkId implements \Stringable
{
    use AggregateRootId;
}
