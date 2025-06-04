<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Infrastructure\Symfony\Bundle;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ProductCatalogBundle extends AbstractBundle implements CompilerPassInterface
{

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass($this);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {

        $container->import(dirname(__DIR__, 2).'/Resources/config/services.php');
        $container->import(dirname(__DIR__, 2).'/Resources/config/{packages}/*.{php,yaml}');

    }

    public function process(ContainerBuilder $container) {

    }
}
