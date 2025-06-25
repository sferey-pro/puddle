<?php

declare(strict_types=1);

namespace App\Shared\Saga\Domain\Repository;

use App\Shared\Saga\Domain\SagaState;
use App\Shared\Saga\Domain\ValueObject\SagaStateId;

/**
 * Définit le contrat pour sauvegarder et récupérer l'état des processus métier (Sagas).
 * C'est le port de notre architecture hexagonale. L'implémentation (l'adaptateur, ex: Doctrine)
 * se chargera de la persistance réelle en base de données.
 */
interface SagaStateRepositoryInterface
{
    public function ofId(SagaStateId $id): ?SagaState;

    public function save(SagaState $sagaState): void;

    /**
     * Trouve une instance de saga en se basant sur une clé et une valeur
     * présentes dans son payload. Essentiel pour la corrélation.
     *
     * @param string $payloadKey La clé dans le tableau payload (ex: 'userId')
     * @param mixed $payloadValue La valeur à rechercher
     */
    public function findOneByPayload(string $payloadKey, mixed $payloadValue): ?SagaState;

    public function nextIdentity(): SagaStateId;
}
