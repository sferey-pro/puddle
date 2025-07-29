<?php

use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Identity\Domain\Service\IdentifierResolverInterface;
use Identity\Domain\Service\IdentifierValidatorInterface;
use Identity\Infrastructure\Persistence\Doctrine\Repository\DoctrineUserIdentityRepository;
use Identity\Infrastructure\Validator\SymfonyIdentifierValidator;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {

    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services->load('Identity\\', '%kernel.project_dir%/src/Identity/')
        ->exclude([
            '%kernel.project_dir%/src/Identity/Domain/{Model,ValueObject,Event}',
            '%kernel.project_dir%/src/Identity/Infrastructure/Symfony/Resources',
        ]);

    // Repositories
    $services->alias(IdentifierValidatorInterface::class, SymfonyIdentifierValidator::class);
    $services->alias(UserIdentityRepositoryInterface::class, DoctrineUserIdentityRepository::class);
};
