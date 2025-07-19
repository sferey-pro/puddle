<?php

namespace Account\Registration\Domain\Repository;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;

/**
 * Contrat de persistance pour l'état du processus de saga d'inscription.
 */
interface RegistrationProcessRepositoryInterface
{
    // ========== CRUD ==========
    public function save(RegistrationSagaProcess $process): void;
    public function remove(RegistrationSagaProcess $process): void;

}
