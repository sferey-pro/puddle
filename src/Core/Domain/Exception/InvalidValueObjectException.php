<?php

declare(strict_types=1);

namespace App\Core\Domain\Exception;

/**
 * Exception de base pour toutes les erreurs de validation d'un Value Object.
 */
abstract class InvalidValueObjectException extends \DomainException implements DomainException
{
    private function __construct(
        string $message,
        private string $errorCode,
        ?\Throwable $previous = null
    ) {
        parent::__construct(message: $message, previous: $previous);
    }

    abstract public function errorCode(): string;
}
