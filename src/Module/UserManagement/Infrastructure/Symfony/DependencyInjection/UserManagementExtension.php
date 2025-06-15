<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class UserManagementExtension extends Extension implements PrependExtensionInterface
{
    public function getAlias(): string
    {
        return 'user_management';
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

    private function configTwig(ContainerBuilder $container) {
        $path = \dirname(__DIR__, 3).'/UI/Resources/templates/';

        if (!$container->hasExtension('twig')) {
            return;
        }

        $container->prependExtensionConfig('twig', ['paths' => [$path => 'UserManagement']]);

        if(!$container->hasExtension('twig_component')) {
            return;
        }

        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'App\\Module\\UserManagement\\UI\\Twig\\Components\\' => '@UserManagement/components/'
            ]
        ]);
    }

    private function configDoctrine(ContainerBuilder $container) {

        $doctrineConfig = [
            'orm' => [
                'mappings' => [
                    'UserManagementBundle' => [
                        'is_bundle' => true,
                        'type' => 'xml',
                        'dir' => "../Doctrine/Mapping",
                        'prefix' => 'App\\Module\\UserManagement\\Domain',
                        'alias' => 'UserManagementBundle',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}
