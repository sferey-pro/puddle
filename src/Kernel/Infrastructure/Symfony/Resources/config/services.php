<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->load('Kernel\\', '%kernel.project_dir%/src/Kernel/')
        ->exclude([
            '%kernel.project_dir%/src/Kernel/**/Domain/{Entity,ValueObject,Event}',
            '%kernel.project_dir%/src/Kernel/**/Infrastructure/Symfony/Resources',
        ]);
};
