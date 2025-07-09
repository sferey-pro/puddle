<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use App\Core\Domain\ValueObject\UniqueValueInterface;
use Assert\Assert;

final readonly class EmailAddress extends AbstractStringValueObject implements UniqueValueInterface
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

    public static function uniqueFieldPath(): string
    {
        return 'email';
    }

    public function uniqueValue(): string
    {
        return $this->value;
    }

}
