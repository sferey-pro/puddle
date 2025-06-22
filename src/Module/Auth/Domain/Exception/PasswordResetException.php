<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Exception;

use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Exception métier unique pour l'entité PasswordReset.
 * Elle centralise toutes les erreurs possibles liées au processus de connexion de reset Password.
 */
final class PasswordResetException extends \DomainException
{
    public const NOT_FOUND = 'APR-001';
    public const EXPIRED = 'APR-002';
    public const ALREADY_USED = 'APR-003';
    public const USER_MISMATCH = 'APR-004';
    public const NOT_FOUND_BY_TOKEN = 'APR-005';
    public const THROTTLING = 'APR-006';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFound(PasswordResetRequestId $id): self
    {
        return new self(\sprintf('Password reset request with ID "%s" not found.', $id), self::NOT_FOUND);
    }

    public static function notFoundByToken(): self
    {
        return new self('The provided password reset token is invalid or does not exist.', self::NOT_FOUND_BY_TOKEN);
    }

    public static function expired(): self
    {
        return new self('The password reset request has expired.', self::EXPIRED);
    }

    public static function throttling(\DateTimeImmutable $nextAttemptAt): self
    {
        return new self('Too many password reset requests.', self::THROTTLING, ['availableAt' => $nextAttemptAt]);
    }

    public static function alreadyUsed(): self
    {
        return new self('This password reset request has already been used.', self::ALREADY_USED);
    }

    public static function userMismatch(UserId $requestUserId, UserId $actualUserId): self
    {
        $message = \sprintf(
            'Password reset request for user "%s" cannot be used by user "%s".',
            $requestUserId,
            $actualUserId
        );
        return new self($message, self::USER_MISMATCH);
    }


    /**
     * Retourne soit le payload complet, soit une valeur spécifique du payload.
     *
     * @param string|null $key La clé à récupérer. Si null, retourne tout le tableau.
     * @return mixed La valeur de la clé, le tableau complet, ou null si la clé n'existe pas.
     */
    public function payload(?string $key = null): mixed
    {
        // Si aucune clé n'est fournie, on retourne tout le tableau.
        if (null === $key) {
            return $this->payload;
        }

        // Si une clé est fournie, on retourne sa valeur, ou null si elle n'existe pas.
        return $this->payload[$key] ?? null;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
