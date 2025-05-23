<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\Auth\Domain\Repository\UserLoginRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserSocialNetworkRepositoryInterface;

use App\Module\Auth\Infrastructure\Doctrine\Repository\UserLoginRepository;
use App\Module\Auth\Infrastructure\Doctrine\Repository\DoctrineUserAccountRepository;
use App\Module\Auth\Infrastructure\Doctrine\Repository\UserSocialNetworkRepository;

return function(ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->load('App\\Module\\Auth\\', dirname(__DIR__, 2).'/src/Module/Auth/')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/Auth/Domain',
            dirname(__DIR__, 2).'/src/Module/Auth/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/Auth/Domain/ValueObjects',
        ]);

    // repositories
    $services->set(UserRepositoryInterface::class)
        ->class(DoctrineUserAccountRepository::class);

    $services->set(UserSocialNetworkRepositoryInterface::class)
        ->class(UserSocialNetworkRepository::class);

    $services->set(UserLoginRepositoryInterface::class)
        ->class(UserLoginRepository::class);

};


