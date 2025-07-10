<?php

namespace Account\Registration\Domain\Repository;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Kernel\Domain\Saga\SagaStateId;

/**
 * Contrat de persistance pour l'état du processus de saga d'inscription.
 */
interface RegistrationProcessRepositoryInterface
{
    /**
     * Trouve un processus de saga d'inscription par son identifiant unique.
     */
    public function ofId(SagaStateId $id): ?RegistrationSagaProcess;

    /**
     * Sauvegarde l'état actuel d'un processus de saga d'inscription.
     */
    public function save(RegistrationSagaProcess $process): void;
}
