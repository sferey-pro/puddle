<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Exception;

use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat Registration.
 */
final class RegistrationException extends \DomainException
{
    private const CAN_REGISTER = 'U-000';
    private const EMAIL_ALREADY_EXISTS = 'U-001';
    private const PHONE_ALREADY_EXISTS = 'U-002';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function canRegister(string $reason): self
    {
        return new self($reason, self::CAN_REGISTER);
    }

    public static function identityAlreadyInUse(mixed $identifier): self
    {
        return match ($identifier::class) {
            EmailAddress::class => self::emailAlreadyExists($identifier),
            PhoneNumber::class => self::phoneAlreadyExists($identifier),
            default => new self('Cet identifiant est déjà utilisé.', "000"),
        };
    }

    public static function emailAlreadyExists(EmailAddress $email): self
    {
        return new self(
            \sprintf('A user with the email "%s" already exists.', $email),
            self::EMAIL_ALREADY_EXISTS
        );
    }

    public static function phoneAlreadyExists(PhoneNumber $phone): self
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
