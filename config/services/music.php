<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;


return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $containerBuilder->setParameter('env(SPOTIFY_CURRENTLY_PLAYING_URL)', 'https://api.spotify.com/v1/me/player/currently-playing');
    $containerBuilder->setParameter('env(SPOTIFY_TOKEN_URL)', 'https://accounts.spotify.com/api/token');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Module\\Music\\', dirname(__DIR__, 2).'/src/Module/Music/')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/Music/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/Music/Domain/ValueObjects',
        ]);
};
