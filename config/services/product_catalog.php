<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Module\ProductCatalog\Domain\Repository\ProductRepositoryInterface;
use App\Module\ProductCatalog\Infrastructure\Doctrine\Repository\DoctrineProductRepository;

return function(ContainerConfigurator $container): void {

    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services->load('App\\Module\\ProductCatalog\\', dirname(__DIR__, 2).'/src/Module/ProductCatalog')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/ProductCatalog/Domain',
            dirname(__DIR__, 2).'/src/Module/ProductCatalog/Domain/ValueObject',
        ]);

    $services->set(ProductRepositoryInterface::class)
        ->class(DoctrineProductRepository::class);
};
