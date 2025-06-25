<?php

declare(strict_types=1);

namespace Tests\Behat\Service;

/**
 * Service simple utilisé par Behat pour indiquer au système
 * qu'un certain type d'action doit échouer.
 */
class FailureSimulator
{
    private array $failures = [];

    public function shouldFailFor(string $key): void
    {
        $this->failures[$key] = true;
    }

    public function mustFail(string $key): bool
    {
        return $this->failures[$key] ?? false;
    }

    public function reset(): void
    {
        $this->failures = [];
    }
}
