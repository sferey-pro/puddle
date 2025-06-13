<?php

declare(strict_types=1);

use App\Module\CostManagement\Application\ReadModel\Repository\CostItemInstanceViewRepositoryInterface;
use App\Module\CostManagement\Application\ReadModel\Repository\RecurringCostViewRepositoryInterface;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\Repository\RecurringCostRepositoryInterface;
use App\Module\CostManagement\Infrastructure\Doctrine\Repository\DoctrineCostItemRepository;
use App\Module\CostManagement\Infrastructure\Doctrine\Repository\DoctrineRecurringCostRepository;
use App\Module\CostManagement\Infrastructure\ReadModel\Repository\DoctrineCostItemInstanceViewRepository;
use App\Module\CostManagement\Infrastructure\ReadModel\Repository\DoctrineRecurringCostViewRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Module\\CostManagement\\', '%kernel.project_dir%/src/Module/CostManagement')
        ->exclude([
            '%kernel.project_dir%/src/Module/CostManagement/Domain',
            '%kernel.project_dir%/src/Module/CostManagement/Infrastructure/Symfony/Resources',
        ]);

    $services->set(CostItemRepositoryInterface::class)
        ->class(DoctrineCostItemRepository::class);

    $services->set(CostItemInstanceViewRepositoryInterface::class)
        ->class(DoctrineCostItemInstanceViewRepository::class);

    $services->set(RecurringCostViewRepositoryInterface::class)
        ->class(DoctrineRecurringCostViewRepository::class);

    $services->set(RecurringCostRepositoryInterface::class)
        ->class(DoctrineRecurringCostRepository::class);
};
