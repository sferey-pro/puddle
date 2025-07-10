<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

final readonly class HashedPassword extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un HashedPassword en cas de succès.
     */
    public static function create(string $hash): Result
    {
        try {
            Assert::that($hash)
                ->notEmpty('Hashed password cannot be empty.');

            return Result::success(new self($hash));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
