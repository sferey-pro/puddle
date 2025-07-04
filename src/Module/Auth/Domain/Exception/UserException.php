<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat User.
 */
final class UserException extends \DomainException
{
    private const NOT_FOUND = 'U-001';
    private const EMAIL_ALREADY_EXISTS = 'U-002';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFound(): self
    {
        return new self('User not found.', self::NOT_FOUND);
    }

    public static function notFoundWithId(UserId $id): self
    {
        return new self(\sprintf('User with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function notFoundWithEmail(Email $email): self
    {
        return new self(\sprintf('User not found with email : %s', $email), self::NOT_FOUND);
    }

    public static function emailAlreadyExists(Email $email): self
    {
        return new self(
            \sprintf('A user with the email "%s" already exists.', $email),
            self::EMAIL_ALREADY_EXISTS
        );
    }

    public function payload(): mixed
    {
        return $this->payload;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
