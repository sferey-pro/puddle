<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\Username;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat User.
 */
final class UserException extends \DomainException
{
    private const NOT_FOUND = 'UM-001';
    private const ALREADY_DEACTIVATED = 'UM-002';
    private const NOT_DEACTIVATED = 'UM-003';
    private const ALREADY_ANONYMIZED = 'UM-004';
    private const EMAIL_ALREADY_EXISTS = 'UM-005';
    private const USERNAME_ALREADY_TAKEN = 'UM-006';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFoundWithId(UserId $id): self
    {
        return new self(\sprintf('User with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function notFoundWithEmail(Email $email): self
    {
        return new self(\sprintf('User not found with email : %s', $email), self::NOT_FOUND);
    }

    public static function accountAlreadyDeactivated(UserId $id): self
    {
        return new self(\sprintf('User account with ID "%s" is already deactivated.', $id), self::ALREADY_DEACTIVATED);
    }

    public static function accountNotDeactivated(UserId $id): self
    {
        return new self(\sprintf('User account with ID "%s" must be deactivated to be reactivated.', $id), self::NOT_DEACTIVATED);
    }

    public static function accountAlreadyAnonymized(UserId $id): self
    {
        return new self(\sprintf('User account with ID "%s" already anonymized".', $id), self::ALREADY_ANONYMIZED);
    }

    public static function emailAlreadyExists(Email $email): self
    {
        return new self(
            \sprintf('A user with the email "%s" already exists.', $email),
            self::EMAIL_ALREADY_EXISTS
        );
    }

    public static function usernameAlreadyTaken(Username $username): self
    {
        return new self(
            \sprintf('Username "%s" is already taken.', $username),
            self::USERNAME_ALREADY_TAKEN
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
