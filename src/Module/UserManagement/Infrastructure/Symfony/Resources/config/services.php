<?php

declare(strict_types=1);

use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\Saga\Step\CreateUserStep;
use App\Module\UserManagement\Domain\Repository\ProfileRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\DoctrineProfileRepository;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use App\Module\UserManagement\Infrastructure\ReadModel\Repository\DoctrineUserViewRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
    ;

    $services->load('App\\Module\\UserManagement\\', '%kernel.project_dir%/src/Module/UserManagement/')
        ->exclude([
            '%kernel.project_dir%/src/Module/UserManagement/Application/ReadModel',
            '%kernel.project_dir%/src/Module/UserManagement/Domain',
            '%kernel.project_dir%/src/Module/UserManagement/Infrastructure/Symfony/Resources',
        ]);

    // repositories
    $services->set(UserRepositoryInterface::class)
        ->class(DoctrineUserRepository::class);

    $services->set(UserViewRepositoryInterface::class)
        ->class(DoctrineUserViewRepository::class);

    $services->set(ProfileRepositoryInterface::class)
        ->class(DoctrineProfileRepository::class);

    $services->set(CreateUserStep::class)
        ->tag('saga.step', ['transition' => 'create_user_profile']);
};
