<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Exception;

use App\Module\Auth\Domain\ValueObject\EmailIdentity;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\PhoneNumber;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\ValueObject\Username;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat User.
 */
final class UserException extends \DomainException
{
    private const NOT_FOUND = 'UM-001';
    private const EMAIL_ALREADY_EXISTS = 'UM-002';
    private const PHONE_ALREADY_EXISTS = 'UM-003';

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

    public static function notFoundWithEmail(EmailAddress $email): self
    {
        return new self(\sprintf('User not found with email : %s', $email), self::NOT_FOUND);
    }

    public static function notFoundWithPhone(PhoneNumber $phone): self
    {
        return new self(\sprintf('User not found with phone : %s', $phone), self::NOT_FOUND);
    }

    public static function identityAlreadyInUse(UserIdentity $identity): self
    {
        return match ($identity::class) {
            EmailIdentity::class => self::emailAlreadyExists($identity->value()),
            PhoneIdentity::class => self::phoneAlreadyExists($identity->value()),
            default => new self('Cet identifiant est déjà utilisé.', "000"),
        };
    }

    public static function emailAlreadyExists(string $email): self
    {
        return new self(
            \sprintf('A user with the email "%s" already exists.', $email),
            self::EMAIL_ALREADY_EXISTS
        );
    }

    public static function phoneAlreadyExists(string $phone): self
    {
        return new self(
            \sprintf('A user with the phone "%s" already exists.', $phone),
            self::PHONE_ALREADY_EXISTS
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
