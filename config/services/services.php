<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set(PdoSessionHandler::class)
        ->args([
            env('DATABASE_URL'),
        ])
    ;

    if ('test' === $container->env()) {
        $services = $container->services()
            ->defaults()
                ->autowire()
                ->autoconfigure()
        ;
        $services->load('Tests\\Behat\\', dirname(__DIR__, 2) . '/tests/Behat/*');
    }
};
