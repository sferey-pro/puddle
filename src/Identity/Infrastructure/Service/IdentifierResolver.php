<?php

namespace Identity\Infrastructure\Service;

use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\PhoneIdentity;
use Identity\Domain\Service\IdentifierResolverInterface;
use Kernel\Domain\Result;
use SharedKernel\Domain\Service\IdentifierAnalyzerInterface;

final class IdentifierResolver implements IdentifierResolverInterface
{
    public function __construct(
        private readonly IdentifierAnalyzerInterface $identifierAnalyzer,
    ) {

    }

    public function resolve(string $value): Result
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

}
