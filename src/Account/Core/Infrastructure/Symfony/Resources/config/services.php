<?php

declare(strict_types=1);

use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Core\Infrastructure\Persistence\Doctrine\Repository\DoctrineAccountRepository;
use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Repository\RegistrationRepositoryInterface;
use Account\Registration\Domain\Specification\CanRegisterSpecification;
use Account\Registration\Infrastructure\Persistence\Doctrine\Repository\DoctrineRegistrationProcessRepository;
use Kernel\Application\Saga\Step\SagaStepInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    $services
        ->instanceof(SagaStepInterface::class)
        ->tag('saga.step');

    $services->load('Account\\', '%kernel.project_dir%/src/Account/')
        ->exclude([
            '%kernel.project_dir%/src/Account/**/Domain/{Entity,ValueObject,Event}',
            '%kernel.project_dir%/src/Account/**/Infrastructure/Symfony/Resources',
        ]);

    $services->set(CanRegisterSpecification::class)
        ->tag('account.domain_specification');

    // Repositories
    $services->alias(AccountRepositoryInterface::class, DoctrineAccountRepository::class);
    $services->alias(RegistrationProcessRepositoryInterface::class, DoctrineRegistrationProcessRepository::class);
    $services->alias(RegistrationRepositoryInterface::class, DoctrineAccountRepository::class);

};
