<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;
use App\Module\ProductCatalog\Infrastructure\Doctrine\Repository\DoctrineProductRepository;

return function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Module\\ProductCatalog\\', '%kernel.project_dir%/src/Module/ProductCatalog')
        ->exclude([
            '%kernel.project_dir%/src/Module/ProductCatalog/Domain',
            '%kernel.project_dir%/src/Module/ProductCatalog/Domain/ValueObject',
            '%kernel.project_dir%/src/Module/ProductCatalog/Infrastructure/Resources',
        ]);

    $services->set(ProductRepositoryInterface::class)
        ->class(DoctrineProductRepository::class);
};
