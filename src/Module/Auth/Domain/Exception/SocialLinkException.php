<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\Auth\Domain\ValueObject\SocialLinkId;

/**
 * Exception de base pour toutes les erreurs métier liées à l'entité SocialLink.
 * Elle centralise toutes les erreurs possibles liées au processus de connexion par un tierce.
 */
final class SocialLinkException extends \DomainException
{
    private const NOT_FOUND = 'USN-001';
    public const ALREADY_ACTIVATED = 'USN-002';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFoundWithId(SocialLinkId $id): self
    {
        return new self(\sprintf('User Social Network with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function alreadyActivated(): self
    {
        return new self('The social link has already been verified.', self::ALREADY_ACTIVATED);
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
