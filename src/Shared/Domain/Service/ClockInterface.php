<?php

declare(strict_types=1);

namespace App\Shared\Domain\Service;


interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}
