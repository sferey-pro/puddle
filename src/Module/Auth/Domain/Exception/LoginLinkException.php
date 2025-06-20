<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\Auth\Domain\ValueObject\LoginLinkId;

/**
 * Exception métier unique pour l'entité LoginLink.
 * Elle centralise toutes les erreurs possibles liées au processus de connexion par lien magique.
 */
final class LoginLinkException extends \DomainException
{
    public const NOT_FOUND = 'AL-001';
    public const EXPIRED = 'AL-002'; // Auth-LoginLink-001
    public const ALREADY_VERIFIED = 'AL-003'; // Auth-LoginLink-002

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFoundWithId(LoginLinkId $id): self
    {
        return new self(\sprintf('Login link with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function notFoundWithHash(Hash $hash): self
    {
        return new self(\sprintf('Login link with Hash "%s" not found.', $hash), self::NOT_FOUND);
    }

    public static function expired(): self
    {
        return new self('The login link has expired.', self::EXPIRED);
    }

    public static function alreadyVerified(): self
    {
        return new self('The login link has already been verified.', self::ALREADY_VERIFIED);
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
