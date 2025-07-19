<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class IdentityExtension extends Extension implements PrependExtensionInterface
{
    private const string BUNDLE_DIR = '%kernel.project_dir%/src/Identity/';

    public function getAlias(): string
    {
        return 'identity';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->configDoctrine($container);
    }

    private function configDoctrine(ContainerBuilder $container)
    {
        $doctrineConfig = [
            'orm' => [
                'mappings' => [
                    'Identity' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => static::BUNDLE_DIR . 'Infrastructure/Persistence/Doctrine/Mapping',
                        'prefix' => 'Identity\Domain',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}
