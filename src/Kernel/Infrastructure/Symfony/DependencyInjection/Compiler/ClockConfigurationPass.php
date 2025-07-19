<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Symfony\DependencyInjection\Compiler;

use Kernel\Application\Clock\ClockInterface;
use Kernel\Infrastructure\Clock\SystemClock;
use Kernel\Infrastructure\Clock\TestClock;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class ClockConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $environment = $container->getParameter('kernel.environment');

        $clockService = match ($environment) {
            'test' => TestClock::class,
            default => SystemClock::class,
        };

        $container->setAlias(ClockInterface::class, $clockService)->setPublic(true);
    }
}
