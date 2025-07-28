<?php

declare(strict_types=1);

namespace Account\Core\Application\Query;

use Kernel\Application\Message\QueryInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final readonly class GetAccountById implements QueryInterface
{
    public function __construct(
        public UserId $userId,
    ) {
    }
}
