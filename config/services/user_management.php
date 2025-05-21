<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use App\Module\UserManagement\Infrastructure\ReadModel\Repository\DoctrineUserViewRepository;

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
        ->class(DoctrineUserRepository::class);

    $services->set(UserViewRepositoryInterface::class)
        ->class(DoctrineUserViewRepository::class);
};
