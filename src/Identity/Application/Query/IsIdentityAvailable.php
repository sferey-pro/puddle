<?php

declare(strict_types=1);

namespace Identity\Application\Query;

use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Message\QueryInterface;

final readonly class IsIdentityAvailable implements QueryInterface
{

    public function __construct(
        private(set) Identifier $identifier
    ) {
    }
}
