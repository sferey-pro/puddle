<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

final readonly class ProductName extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un ProductName en cas de succès.
     */
    public static function create(string $email): Result
    {
        try {
            $normalizedEmail = strtolower($email);
            Assert::that($normalizedEmail)
                ->notEmpty('Product name cannot be empty.')
                ->maxLength(180, 'Product name cannot exceed 180 characters.');

            return Result::success(new self($normalizedEmail));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
