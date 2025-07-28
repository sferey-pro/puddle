<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;


interface AccountRegistrationContextInterface
{
    public function initiateRegistration(string $identifier, string $ipAddress): void;
}
