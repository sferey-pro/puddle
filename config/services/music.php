<?php

declare(strict_types=1);

use App\Module\Music\Domain\Port\CurrentlyPlayingMusicProviderInterface;
use App\Module\Music\Infrastructure\SpotifyAuthorizationCodeTokenFetcher;
use App\Module\Music\Infrastructure\Provider\SpotifyCurrentlyPlayingMusicProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Module\\Music\\', dirname(__DIR__, 2).'/src/Module/Music/')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/Music/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/Music/Domain/ValueObjects',
        ]);

    // Port & Adapter
    $services->set(CurrentlyPlayingMusicProviderInterface::class)
        ->class(SpotifyCurrentlyPlayingMusicProvider::class);

};
