<?php

declare(strict_types=1);

namespace App\Module\Core\Domain\ValueObject;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\Phone;

final class IdentifierFactory
{
    /**
     * @return Result<Email|Phone, InvalidIdentifierFormatError>
     */
    public static function create(string $identifier): Result
    {
        $emailResult = Email::fromString($identifier);
        if ($emailResult->isSuccess()) {
            return $emailResult;
        }

        $phoneResult = Phone::fromString($identifier);
        if ($phoneResult->isSuccess()) {
            return $phoneResult;
        }

        return Result::failure(new InvalidIdentifierFormatError($identifier));
    }
}
