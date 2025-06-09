<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Symfony\Scheduler\Task;

use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('*/10 * * * *')]
class CostItemsReconcile
{
    public function __invoke()
    {
        new RunCommandMessage('puddle:cost-items:reconcile');
    }
}
