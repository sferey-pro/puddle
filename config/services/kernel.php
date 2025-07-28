<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Uid\Command\GenerateUuidCommand;
use Symfony\Component\Uid\Command\InspectUuidCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();


    $services->load('DataFixtures\\', dirname(__DIR__, 2) . '/fixtures');

    if ('dev' === $containerConfigurator->env()) {
        $services
            ->set(GenerateUuidCommand::class)
            ->set(InspectUuidCommand::class);
    }
};
