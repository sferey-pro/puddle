<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Infrastructure\Symfony;

use App\Module\ProductCatalog\Infrastructure\Symfony\DependencyInjection\ProductCatalogExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProductCatalogBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new ProductCatalogExtension();
        }

        return $this->extension ?: null;
    }
}
