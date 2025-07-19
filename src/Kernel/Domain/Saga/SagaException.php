<?php

declare(strict_types=1);

namespace Kernel\Domain\Saga;

class SagaException extends \RuntimeException
{
    private const string EXECUTION_FAILED = 'S-001';

    private function __construct(string $message, private string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    /**
     * Crée une exception indiquant qu'un parcours métier spécifique a échoué.
     */
    public static function executionFailed(SagaStateId $id): self
    {
        return new self(\sprintf('Saga with ID "%s" failed to execute.', $id), self::EXECUTION_FAILED);
    }
}
