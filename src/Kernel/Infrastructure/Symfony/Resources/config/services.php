<?php

declare(strict_types=1);

use Kernel\Application\Notifier\NotifierFactoryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services
        ->instanceof(NotifierFactoryInterface::class)
        ->tag('app.notification_factory');

    $services->load('Kernel\\', '%kernel.project_dir%/src/Kernel/')
        ->exclude([
            '%kernel.project_dir%/src/Kernel/Domain/{Model,ValueObject,Event}',
            '%kernel.project_dir%/src/Kernel/Infrastructure/Symfony/Resources',
        ]);
};
