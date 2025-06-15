<?php

declare(strict_types=1);

use App\Module\Auth\Domain\Repository\UserLoginRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserSocialNetworkRepositoryInterface;
use App\Module\Auth\Infrastructure\Doctrine\Repository\DoctrineUserAccountRepository;
use App\Module\Auth\Infrastructure\Doctrine\Repository\UserLoginRepository;
use App\Module\Auth\Infrastructure\Doctrine\Repository\UserSocialNetworkRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->load('App\\Module\\Auth\\', '%kernel.project_dir%/src/Module/Auth/')
        ->exclude([
            '%kernel.project_dir%/src/Module/Auth/Domain',
            '%kernel.project_dir%/src/Module/Auth/Infrastructure/Symfony/Resources',
        ]);

    // repositories
    $services->set(UserRepositoryInterface::class)
        ->class(DoctrineUserAccountRepository::class);

    $services->set(UserSocialNetworkRepositoryInterface::class)
        ->class(UserSocialNetworkRepository::class);

    $services->set(UserLoginRepositoryInterface::class)
        ->class(UserLoginRepository::class);
};
