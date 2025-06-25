<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Saga;

class SagaExecutionFailedException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Execution of saga failed');
    }
}
