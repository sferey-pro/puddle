<?php

declare(strict_types=1);

namespace Identity\Domain\Exception;

use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat Identity.
 */
final class IdentityException extends \DomainException
{
    private const string IDENTITY_ALREADY_EXISTS = 'I-001';
    private const string IDENTITY_NOT_FOUND = 'I-002';
    private const string CANNOT_REMOVE_PRIMARY_IDENTITY = 'I-003';
    private const string CANNOT_REMOVE_LAST_IDENTITY = 'I-004';


    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function identityAlreadyExists(): self
    {
        return new self('This identity is already attached to another account', self::IDENTITY_ALREADY_EXISTS);
    }

    public static function userIdentityNotFound(UserId $userId): self
    {
        return new self(sprintf('UserIdentity for ID "%s" not found.', (string) $userId), self::IDENTITY_NOT_FOUND);
    }

    public static function identityNotFound(Identifier $identifier): self
    {
        return new self(sprintf('Identity "%s" of type "%s" not found for this account.', $identifier->value(), $identifier->getClass()), self::IDENTITY_NOT_FOUND);
    }

    public static function cannotRemoveLastIdentity(): self
    {
        return new self('Cannot remove the last identity from an account. An account must have at least one identity.', self::CANNOT_REMOVE_LAST_IDENTITY);
    }

    public static function cannotRemovePrimaryIdentity(): self
    {
        return new self('Cannot remove the primary identity. Detach other identities first or force removal.', self::CANNOT_REMOVE_PRIMARY_IDENTITY);
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
