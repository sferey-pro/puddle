<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Core\Domain\ValueObject\AbstractStringValueObject;
use Assert\Assert;

final readonly class IpAddress extends AbstractStringValueObject
{
    /**
     * @return Result<self> Un Result contenant un IpAddress en cas de succès.
     */
    public static function create(string $ip): Result
    {
        try {
            Assert::that($ip)
                ->ip('Invalid IP address.');

            return Result::success(new self($ip));
        } catch (\InvalidArgumentException $e) {
            return Result::failure(new \DomainException($e->getMessage()));
        }
    }
}
