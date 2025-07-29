<?php

declare(strict_types=1);

use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Repository\LoginAttemptRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Infrastructure\Persistence\Doctrine\Repository\DoctrineAccessCredentialRepository;
use Authentication\Infrastructure\Persistence\Doctrine\Repository\DoctrineLoginAttemptRepository;
use Authentication\Infrastructure\Service\SymfonyTokenGeneratorAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;


    $services->load('Authentication\\', '%kernel.project_dir%/src/Authentication/')
        ->exclude([
            '%kernel.project_dir%/src/Authentication/Domain/{Model,ValueObject,Event}',
            '%kernel.project_dir%/src/Authentication/Infrastructure/Symfony/Resources',
        ]);

    // Repositories
    $services->alias(AccessCredentialRepositoryInterface::class, DoctrineAccessCredentialRepository::class);
    $services->alias(LoginAttemptRepositoryInterface::class, DoctrineLoginAttemptRepository::class);
};
