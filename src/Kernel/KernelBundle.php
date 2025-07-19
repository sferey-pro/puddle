<?php

declare(strict_types=1);

namespace Kernel;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KernelBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }
}
