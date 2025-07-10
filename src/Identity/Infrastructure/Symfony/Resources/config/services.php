<?php

use Identity\Application\Saga\Step\AttachIdentityStep;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {

    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

     $services->load('Identity\\', '%kernel.project_dir%/src/Identity/')
        ->exclude([
            '%kernel.project_dir%/src/Identity/**/Domain',
            '%kernel.project_dir%/src/Identity/**/Infrastructure/Symfony/Resources',
        ]);

    $services->set(AttachIdentityStep::class)
        ->tag('saga.step', ['transition' => 'trigger_welcome_link']);
};
