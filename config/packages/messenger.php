<?php

declare(strict_types=1);

use App\Common\Command\CommandInterface;
use App\Common\Query\QueryInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('framework', [
        'messenger' => [
            'default_bus' => 'command.bus',
            'buses' => [
                'command.bus' => 'test' === $containerConfigurator->env() ? [] : [
                    'middleware' => [
                        'messenger.middleware.doctrine_transaction',
                    ],
                ],
                'query.bus' => [],
            ],
            'transports' => [
                'sync' => 'sync://',
            ],
            'routing' => [
                QueryInterface::class => 'sync',
                CommandInterface::class => 'sync',
            ],
        ],
    ]);
};
