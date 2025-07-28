<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObject;

use Kernel\Domain\Exception\ValidationException;
use Kernel\Domain\Result;

/**
 * Trait pour simplifier la création de ValueObjects validés.
 */
trait ValidatedValueObjectTrait
{
    /**
     * Template method pour la validation.
     *
     * @return Result<static>
     */
    public static function create(...$args): Result
    {
        try {
            static::validate(...$args);
            return Result::success(new static(...$args));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(
                new ValidationException(
                    sprintf('%s validation failed: %s', static::class, $e->getMessage()),
                    previous: $e
                )
            );
        }
    }

    /**
     * Factory method qui throw directement.
     * Utile dans les contextes où on est sûr de la validité.
     */
    public static function fromValidatedValue(...$args): static
    {
        $result = static::create(...$args);
        return $result->valueOrThrow();
    }

    /**
     * À implémenter dans chaque ValueObject.
     */
    abstract protected static function validate(...$args): void;
}
