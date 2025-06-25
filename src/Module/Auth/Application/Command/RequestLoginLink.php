<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use Webmozart\Assert\Assert;

final readonly class RequestLoginLink implements CommandInterface
{
    public function __construct(
        public string $identifier,
        public IpAddress $ipAddress,
    ) {
        Assert::notEmpty($identifier);
    }
}
