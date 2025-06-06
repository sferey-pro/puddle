<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('DataFixtures\\', dirname(__DIR__, 2) . '/fixtures');

    $services->load('App\\Module\\Static\\', dirname(__DIR__, 2) . '/src/Module/Static/');
};
