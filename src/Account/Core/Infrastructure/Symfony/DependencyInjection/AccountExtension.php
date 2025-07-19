<?php

declare(strict_types=1);

namespace Account\Core\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class AccountExtension extends Extension implements PrependExtensionInterface
{
    private const string BUNDLE_DIR = '%kernel.project_dir%/src/Account/';

    public function getAlias(): string
    {
        return 'account';
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

    private function configDoctrine(ContainerBuilder $container): void
    {
        $doctrineConfig = [
            'orm' => [
                'mappings' => [
                    'Account' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => static::BUNDLE_DIR . 'Core/Infrastructure/Persistence/Doctrine/Mapping',
                        'prefix' => 'Account\Core\Domain',
                    ],
                    'Registration' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => static::BUNDLE_DIR . 'Registration/Infrastructure/Persistence/Doctrine/Mapping',
                        'prefix' => 'Account\Registration\Domain',
                    ],
                    'Lifecycle' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => static::BUNDLE_DIR . 'Lifecycle/Infrastructure/Persistence/Doctrine/Mapping',
                        'prefix' => 'Account\Lifecycle\Domain',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}
