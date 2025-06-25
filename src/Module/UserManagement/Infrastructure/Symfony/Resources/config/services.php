<?php

declare(strict_types=1);

use App\Core\Application\Validator\UniqueConstraintCheckerInterface;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\ProfileRepositoryInterface;
use App\Module\UserManagement\Domain\Repository\UserRepositoryInterface;
use App\Module\UserManagement\Domain\Specification\UniqueEmailSpecification;
use App\Module\UserManagement\Domain\Specification\UniqueUsernameSpecification;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\DoctrineProfileRepository;
use App\Module\UserManagement\Infrastructure\Doctrine\Repository\DoctrineUserRepository;
use App\Module\UserManagement\Infrastructure\ReadModel\Repository\DoctrineUserViewRepository;
use App\Module\UserManagement\Infrastructure\Service\UserUniqueConstraintChecker;
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

    $services->set(UniqueConstraintCheckerInterface::class)
        ->class(UserUniqueConstraintChecker::class);

    // Services de Domaine / Spécifications (maintenant injectées avec le vérificateur générique)
    $services->set(UniqueEmailSpecification::class);
    $services->set(UniqueUsernameSpecification::class);
};
