<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class AuthenticationExtension extends Extension implements PrependExtensionInterface
{
    private const string BUNDLE_DIR = '%kernel.project_dir%/src/Authentication/';

    public function getAlias(): string
    {
        return 'authentication';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.php');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->configureTwig($container);
        $this->configureTwigComponents($container);
        $this->configDoctrine($container);
    }

    private function configDoctrine(ContainerBuilder $container): void
    {
        $doctrineConfig = [
            'orm' => [
                'mappings' => [
                    'Authentication' => [
                        'is_bundle' => false,
                        'type' => 'xml',
                        'dir' => static::BUNDLE_DIR . 'Infrastructure/Persistence/Doctrine/Mapping',
                        'prefix' => 'Authentication\Domain',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }

    private function configureTwig(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'paths' => [
                static::BUNDLE_DIR . 'Presentation/Resources/templates' => 'Authentication',
            ],
        ]);
    }

    private function configureTwigComponents(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Authentication\\Presentation\\Twig\\Components\\' => '@Authentication/components/',
            ],
        ]);
    }
}
