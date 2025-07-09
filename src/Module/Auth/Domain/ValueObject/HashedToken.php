<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

final readonly class HashedToken extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un HashedToken en cas de succès.
     */
    public static function create(string $hashedToken): Result
    {
        try {
            Assert::that($hashedToken)
                ->notEmpty('Hashed token cannot be empty.')
                ->length(64, 'Hashed token must be 64 characters long.');

            return Result::success(new self($hashedToken));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
