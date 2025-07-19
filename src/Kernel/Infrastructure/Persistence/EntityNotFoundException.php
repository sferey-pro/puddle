<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence;

class EntityNotFoundException extends \DomainException implements \Throwable
{
    public static function fromId(mixed $id, string $className): self
    {
        return new self(sprintf(
            'Entity "%s" with ID "%s" not found.',
            $className,
            (string) $id
        ));
        
    }

    public static function fromCriteria(array $criteria, string $className): self
    {
        $criteriaString = json_encode($criteria);
        return new self(sprintf(
            'Entity "%s" with criteria "%s" not found.',
            $className,
            $criteriaString
        ));
    }
}
