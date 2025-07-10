<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

final readonly class EmailAddress extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un EmailAddress en cas de succès.
     */
    public static function create(string $email): Result
    {
        try {
            $normalizedEmail = strtolower($email);
            Assert::that($normalizedEmail)
                ->notEmpty('Email address cannot be empty.')
                ->maxLength(180, 'Email address cannot exceed 180 characters.')
                ->email('"%s" is not a valid email address.');

            return Result::success(new self($normalizedEmail));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
