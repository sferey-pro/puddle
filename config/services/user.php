<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\User\Domain\Repository\UserRepositoryInterface;
use App\Module\User\Infrastructure\Doctrine\Repository\UserRepository;

return function(ContainerConfigurator $container): void {

    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Module\\User\\', dirname(__DIR__, 2).'/src/Module/User')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/User/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/User/Domain/ValueObjects',
        ]);

    $services->set(UserRepositoryInterface::class)
        ->class(UserRepository::class);
};
