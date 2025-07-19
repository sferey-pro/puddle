<?php

declare(strict_types=1);

namespace Authentication\Domain\Exception;


final class InvalidIdentifierException extends AuthenticationException
{
    protected string $errorCode = 'AUTH_005';

    public static function withMessage(string $message): self
    {
        return new self($message);
    }
}
