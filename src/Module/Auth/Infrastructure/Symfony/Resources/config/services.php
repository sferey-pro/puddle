<?php

declare(strict_types=1);

use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\LoginLinkGeneratorInterface;
use App\Module\Auth\Domain\Service\PasswordResetTokenGeneratorInterface;
use App\Module\Auth\Domain\Specification\UniqueEmailSpecification;
use App\Module\Auth\Infrastructure\Doctrine\Repository\DoctrinePasswordResetRequestRepository;
use App\Module\Auth\Infrastructure\Doctrine\Repository\DoctrineUserAccountRepository;
use App\Module\Auth\Infrastructure\Service\AuthUniqueConstraintChecker;
use App\Module\Auth\Infrastructure\Service\SecureTokenGenerator;
use App\Module\Auth\Infrastructure\Symfony\Service\LoginLinkGenerator;
use App\Shared\Domain\Service\UniqueConstraintCheckerInterface;
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

    $services->load('App\\Module\\Auth\\Domain\\Service\\', '%kernel.project_dir%/src/Module/Auth/Domain/Service/');

    // repositories
    $services->set(UserRepositoryInterface::class)
        ->class(DoctrineUserAccountRepository::class);

    $services->alias(PasswordResetRequestRepositoryInterface::class, DoctrinePasswordResetRequestRepository::class);

    $services->set(UniqueConstraintCheckerInterface::class)
        ->class(AuthUniqueConstraintChecker::class);

    // Services de Domaine / Spécifications (maintenant injectées avec le vérificateur générique)
    $services->set(UniqueEmailSpecification::class);

    $services->set(LoginLinkGeneratorInterface::class)
        ->class(LoginLinkGenerator::class);

    $services->alias(PasswordResetTokenGeneratorInterface::class, SecureTokenGenerator::class);
};
