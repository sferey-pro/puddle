<?php

declare(strict_types=1);

use Account\Core\Infrastructure\Adapter\Out\AccountContextService;
use Account\Registration\Application\Service\AccountRegistrationContextService;
use Authentication\Infrastructure\Adapter\Out\AuthenticationContextService;
use Identity\Infrastructure\Adapter\Out\IdentityContextService;
use SharedKernel\Domain\Service\AccountContextInterface;
use SharedKernel\Domain\Service\AccountRegistrationContextInterface;
use SharedKernel\Domain\Service\AuthenticationContextInterface;
use SharedKernel\Domain\Service\IdentityContextInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;


    $services->load('SharedKernel\\', '%kernel.project_dir%/src/SharedKernel/')
        ->exclude([
            '%kernel.project_dir%/src/SharedKernel/Domain/{Model,ValueObject,Event}',
            '%kernel.project_dir%/src/SharedKernel/Infrastructure/Symfony/Resources',
        ]);

    // Adapters
    $services->alias(IdentityContextInterface::class, IdentityContextService::class);
    $services->alias(AuthenticationContextInterface::class, AuthenticationContextService::class);
    $services->alias(AccountRegistrationContextInterface::class, AccountRegistrationContextService::class);
    $services->alias(AccountContextInterface::class, AccountContextService::class);
};
