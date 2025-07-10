<?php

namespace Account\Registration\Domain\Repository;

use Account\Registration\Domain\RegistrationConfig;

interface RegistrationConfigRepositoryInterface
{
    public function getActive(): RegistrationConfig;

    public function getTotalRegistrations(): int;
}
