<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\UserRepository;

return function(ContainerConfigurator $container): void {

    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Module\\UserManagement\\', dirname(__DIR__, 2).'/src/Module/UserManagement')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/UserManagement/Domain',
            dirname(__DIR__, 2).'/src/Module/UserManagement/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/UserManagement/Domain/ValueObjects',
        ]);

    $services->set(UserRepositoryInterface::class)
        ->class(UserRepository::class);
};
