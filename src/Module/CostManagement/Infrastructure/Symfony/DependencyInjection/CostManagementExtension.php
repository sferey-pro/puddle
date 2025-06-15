<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class CostManagementExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'cost_management';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->configTwig($container);
        $this->configDoctrine($container);
    }

    private function configTwig(ContainerBuilder $container)
    {
        $path = \dirname(__DIR__, 3).'/UI/Resources/templates/';

        if (!$container->hasExtension('twig')) {
            return;
        }

        $container->prependExtensionConfig('twig', ['paths' => [$path => 'CostManagement']]);

        if (!$container->hasExtension('twig_component')) {
            return;
        }

        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'App\\Module\\CostManagement\\UI\\Twig\\Components\\' => '@CostManagement/components/',
            ],
        ]);
    }

    private function configDoctrine(ContainerBuilder $container)
    {
        $doctrineConfig = [
            'orm' => [
                'mappings' => [
                    'CostManagementBundle' => [
                        'is_bundle' => true,
                        'type' => 'xml',
                        'dir' => '../Doctrine/Mapping',
                        'prefix' => 'App\\Module\\CostManagement\\Domain',
                        'alias' => 'CostManagementBundle',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}
