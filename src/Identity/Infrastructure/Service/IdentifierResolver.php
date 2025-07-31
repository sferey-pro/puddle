<?php

namespace Identity\Infrastructure\Service;

use Identity\Domain\Exception\InvalidIdentifierException;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Identity\Domain\Service\IdentifierResolverInterface;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Result;
use SharedKernel\Domain\Service\IdentifierAnalyzerInterface;

final class IdentifierResolver implements IdentifierResolverInterface
{
    public function __construct(
        private readonly IdentifierAnalyzerInterface $identifierAnalyzer,
    ) {
    }

    private function execute(string $value): Result
    {
        $analysis = $this->identifierAnalyzer->analyze($value);

        if (!$analysis->isValid) {
            return Result::failure(
                new \InvalidArgumentException('Identifier not valid')
            );
        }

        $identifier = match($analysis->type) {
            'email' => EmailIdentity::create($value),
            'phone' => PhoneIdentity::create($value),
            default => throw new \InvalidArgumentException('Unknown identifier type')
        };

        return $identifier;
    }


    public function resolve(string $rawIdentifier): Identifier
    {
        $result = $this->execute($rawIdentifier);

        if ($result->isFailure()) {
            throw InvalidIdentifierException::fromResolutionError(
                $rawIdentifier,
                $result->error->getMessage()
            );
        }

        return $result->value();
    }


    public function tryResolveIdentifier(string $rawIdentifier): ?Identifier
    {
        try {
            return $this->resolve($rawIdentifier);
        } catch (InvalidIdentifierException) {
            return null;
        }
    }

    public function resolveMultiple(array $rawIdentifiers): array
    {
        $resolved = [];

        foreach ($rawIdentifiers as $raw) {
            $identifier = $this->tryResolveIdentifier($raw);
            if ($identifier) {
                $resolved[] = $identifier;
            }
        }

        return $resolved;
    }
}
