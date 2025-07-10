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
    const BUNDLE_DIR = '%kernel.project_dir%/src/Account/';

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
        $this->configTwig($container);
        $this->configTwigComponent($container);
        $this->configDoctrine($container);
    }

    private function configTwig(ContainerBuilder $container)
    {
        if (!$container->hasExtension('twig')) {
            return;
        }

        $container->prependExtensionConfig('twig', [
            'paths' => [
                static::BUNDLE_DIR . 'Registration/Presentation/Resources/templates/' => 'AccountRegistration',
                static::BUNDLE_DIR . 'Profile/Presentation/Resources/templates/' => 'AccountProfile',
                static::BUNDLE_DIR . 'Core/Presentation/Resources/templates/' => 'AccountCore',
            ],
        ]);
    }

    private function configTwigComponent(ContainerBuilder $container)
    {
        if (!$container->hasExtension('twig_component')) {
            return;
        }

        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Account\\Registration\\Presentation\\Twig\\Components' => '@AccountRegistration/components/',
                'Account\\Profile\\Presentation\\Twig\\Components' => '@AccountProfile/components/',
                'Account\\Core\\Presentation\\Twig\\Components' => '@AccountCore/components/',
            ],
        ]);
    }

    private function configDoctrine(ContainerBuilder $container)
    {
        $doctrineConfig = [
            'orm' => [
                'mappings' => [
                    'AccountBundle' => [
                        'is_bundle' => true,
                        'type' => 'xml',
                        'dir' => '../Doctrine/Mapping',
                        'prefix' => 'App\\Module\\Auth\\Domain',
                        'alias' => 'AccountBundle',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $doctrineConfig);
    }
}
