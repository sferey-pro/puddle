<?php

declare(strict_types=1);

use App\Core\Application\Event\EventBusInterface;
use App\Core\Application\Clock\ClockInterface;
use App\Core\Infrastructure\Bus\MessengerEventBus;
use App\Core\Infrastructure\Clock\SystemClock;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Uid\Command\GenerateUuidCommand;
use Symfony\Component\Uid\Command\InspectUuidCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Core\\', dirname(__DIR__, 2).'/src/Core');

    $services->load('App\\Shared\\', dirname(__DIR__, 2).'/src/Shared');

    $services->load('App\\Module\\SharedContext\\', dirname(__DIR__, 2).'/src/Module/SharedContext')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/SharedContext/Domain/',
        ]);

    $services->set(ClockInterface::class, SystemClock::class);

    $services
        ->alias(EventBusInterface::class, 'event.bus');

    $services->set(EventBusInterface::class)
        ->class(MessengerEventBus::class);

    if ('dev' === $containerConfigurator->env()) {
        $services
            ->set(GenerateUuidCommand::class)
            ->set(InspectUuidCommand::class);
    }
};
