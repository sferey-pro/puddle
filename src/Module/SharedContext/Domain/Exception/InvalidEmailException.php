<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\Exception;

use App\Core\Domain\Exception\InvalidValueObjectException;

final class InvalidEmailException extends InvalidValueObjectException
{
    private const NOT_VALID = 'VO-001';

    public static function fromValue(string $email): self {
        return new self(\sprintf('The email "%s" is not valid.', $email));
    }

    public function errorCode(): string {
        return self::NOT_VALID;
    }
}
