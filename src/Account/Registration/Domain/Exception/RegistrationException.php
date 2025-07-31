<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Exception;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat Registration.
 */
final class RegistrationException extends \DomainException
{
    private const string CAN_REGISTER = 'U-000';
    private const string ALREADY_IN_PROGRESS = 'U-001';
    private const string ALREADY_EXISTS = 'U-002';
    private const string INVALID_IDENTIFIER = 'U-003';
    private const string PROCESS_NOT_FOUND = 'U-004';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function canRegister(string $reason): self
    {
        return new self(sprintf('Registration not allowed: %s', $reason), self::CAN_REGISTER);
    }

    public static function alreadyInProgress(string $identifier): self
    {
        return new self(sprintf(
            'A registration process is already in progress for identifier: %s',
            $identifier
        ), self::ALREADY_IN_PROGRESS);
    }

    public static function invalidIdentifier(string $identifier): self
    {
        return new self(sprintf('Invalid identifier format: %s', $identifier), self::INVALID_IDENTIFIER);
    }

    public static function processNotFound(string $sagaId): self
    {
        return new self(sprintf('Registration process not found: %s', $sagaId), self::PROCESS_NOT_FOUND);
    }

    public static function identifierAlreadyExists(string $identifier): self
    {
        return new self(sprintf('An account with identifier "%s" already exists.', $identifier), self::ALREADY_EXISTS);
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
