<?php

declare(strict_types=1);

namespace Account\Registration\Infrastructure\Service;

use Account\Registration\Application\Service\IdentifierResolverInterface;
use Identity\Application\Service\IdentifierResolver as UpstreamResolver;
use Identity\Domain\ValueObject\UserIdentity;
use Kernel\Domain\Result;
use LogicException;

final class IdentityServiceAdapter implements IdentifierResolverInterface
{
    public function __construct(
        private readonly UpstreamResolver $resolver
    ) {
    }

    public function resolve(string $value): Result
    {
        return $this->resolver->resolve($value);
    }
}
