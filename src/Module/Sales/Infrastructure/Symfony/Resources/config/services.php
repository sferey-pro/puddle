<?php

declare(strict_types=1);

use App\Module\Sales\Application\EventListener\DomainExceptionListener;
use App\Module\Sales\Application\ReadModel\Repository\OrderViewRepositoryInterface;
use App\Module\Sales\Domain\Repository\OrderRepositoryInterface;
use App\Module\Sales\Infrastructure\Doctrine\Repository\DoctrineOrderRepository;
use App\Module\Sales\Infrastructure\ReadModel\Repository\DoctrineOrderViewRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Module\\Sales\\', '%kernel.project_dir%/src/Module/Sales')
        ->exclude([
            '%kernel.project_dir%/src/Module/Sales/Domain',
            '%kernel.project_dir%/src/Module/Sales/Infrastructure/Symfony/Resources',
        ])
    ;

    $services->set(OrderRepositoryInterface::class)
        ->class(DoctrineOrderRepository::class)
    ;

    $services->set(OrderViewRepositoryInterface::class)
        ->class(DoctrineOrderViewRepository::class)
    ;

    $services->set(DomainExceptionListener::class)
        ->tag('kernel.event_listener')
    ;
};
