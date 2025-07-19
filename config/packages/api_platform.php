<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('api_platform', [
        'title' => 'API Puddle',
        'version' => '0.0.1',
        'show_webby' => false,
        'defaults' => [
            'stateless' => true,
            'cache_headers' => [
                'vary' => ['Content-Type', 'Authorization', 'Origin']
            ]
        ],

        'mapping' => [
            'paths' => [
                // '%kernel.project_dir%/src/Module/Sales/Infrastructure/Symfony/Resources/config/api_platform/resources',
            ],
        ],

        'defaults' => [
            'route_prefix' => 'v1'
        ]
    ]);
};
