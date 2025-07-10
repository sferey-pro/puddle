<?php

declare(strict_types=1);

use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Core\Infrastructure\Persistence\Doctrine\Repository\DoctrineAccountRepository;
use Account\Registration\Application\Saga\Step\CreateAccountStep;
use Account\Registration\Application\Saga\Step\TriggerWelcomeNotificationStep;
use Kernel\Application\Saga\Step\SagaStepInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    // $services
    //     ->instanceof(WelcomeNotifierInterface::class)
    //         ->tag('app.welcome_notifier');

    $services
        ->instanceof(SagaStepInterface::class)
            ->tag('saga.step');

    $services->load('Account\\', '%kernel.project_dir%/src/Account/')
        ->exclude([
            '%kernel.project_dir%/src/Account/**/Domain',
            '%kernel.project_dir%/src/Account/**/Infrastructure/Symfony/Resources',
        ]);

    // repositories
    // $services->set(AccountRepositoryInterface::class)
    //     ->class(DoctrineAccountRepository::class);
};
