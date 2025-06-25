<?php

declare(strict_types=1);

namespace App\Shared\Saga\Domain\Exception;

use App\Shared\Saga\Domain\Enum\SagaStatus;
use App\Shared\Saga\Domain\ValueObject\SagaStateId;

/**
 * Exception métier unique pour le mécanisme de Saga.
 * * Centralise toutes les erreurs prévisibles qui peuvent survenir lors de l'orchestration
 * d'un processus métier, comme une tentative d'opération sur une saga dans un état invalide.
 */
final class SagaException extends \DomainException
{
    private const INVALID_STATE_OPERATION = 'SAGA-001';
    private const NOT_FOUND = 'SAGA-002';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques,
     * garantissant ainsi que chaque exception est créée avec un message et un code d'erreur standardisés.
     */
    private function __construct(string $message, private readonly string $errorCode, private mixed $payload = null, ?\Throwable $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    /**
     * Levée lorsqu'une opération est tentée sur une Saga alors qu'elle n'est pas
     * dans le statut requis pour cette opération (ex: démarrer une Saga déjà `COMPLETED`).
     */
    public static function invalidStateForOperation(string $operation, SagaStateId $id, SagaStatus $currentStatus): self
    {
        $message = \sprintf(
            'Cannot perform operation "%s" on saga %s because its current status is "%s".',
            $operation,
            $id,
            $currentStatus->value
        );

        return new self($message, self::INVALID_STATE_OPERATION, ['id' => $id, 'status' => $currentStatus->value]);
    }

    /**
     * Levée lorsqu'une instance de SagaState ne peut pas être trouvée en base de données.
     */
    public static function notFoundWithId(SagaStateId $id): self
    {
        $message = \sprintf('Saga state with ID "%s" not found.', $id);

        return new self($message, self::NOT_FOUND, ['id' => $id]);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function payload(?string $key = null): mixed
    {
        if (null === $key) {
            return $this->payload;
        }

        return $this->payload[$key] ?? null;
    }
}
