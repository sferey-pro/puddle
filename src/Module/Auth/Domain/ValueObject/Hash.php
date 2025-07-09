<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;

final readonly class Hash extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un Hash en cas de succès.
     */
    public static function create(string $hash): Result
    {
        try {
            return Result::success(new self($hash));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}