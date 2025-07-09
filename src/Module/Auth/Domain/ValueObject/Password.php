<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

final readonly class Password extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un Password en cas de succès.
     */
    public static function create(string $password): Result
    {
        try {

            return Result::success(new self($password));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
