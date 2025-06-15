<?php

declare(strict_types=1);

use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use App\Module\UserManagement\Infrastructure\ReadModel\Repository\DoctrineUserViewRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->load('App\\Module\\UserManagement\\', '%kernel.project_dir%/src/Module/UserManagement/')
        ->exclude([
            '%kernel.project_dir%/src/Module/UserManagement/Domain',
            '%kernel.project_dir%/src/Module/UserManagement/Infrastructure/Symfony/Resources',
        ]);

    // repositories
    $services->set(UserRepositoryInterface::class)
        ->class(DoctrineUserRepository::class);

    $services->set(UserViewRepositoryInterface::class)
        ->class(DoctrineUserViewRepository::class);
};
