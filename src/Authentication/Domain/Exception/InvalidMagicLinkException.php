<?php

declare(strict_types=1);

namespace Authentication\Domain\Exception;

/**
 * Invalid credential exceptions
 */
final class InvalidMagicLinkException extends AuthenticationException
{
    protected string $errorCode = 'AUTH_001';

    public static function expired(): self
    {
        return new self('This magic link has expired. Please request a new one.');
    }

    public static function alreadyUsed(): self
    {
        return new self('This magic link has already been used.');
    }

    public static function notFound(): self
    {
        return new self('Invalid magic link.');
    }

    public static function tampered(): self
    {
        return new self('This magic link appears to be invalid or tampered with.');
    }
}
