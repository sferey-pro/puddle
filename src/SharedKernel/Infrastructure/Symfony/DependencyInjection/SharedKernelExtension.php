<?php

declare(strict_types=1);

namespace SharedKernel\Infrastructure\Symfony\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class SharedKernelExtension extends Extension implements PrependExtensionInterface
{
    private const string BUNDLE_DIR = '%kernel.project_dir%/src/SharedKernel/';

    public function getAlias(): string
    {
        return 'shared_kernel';
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
    }

    private function configureTwig(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig', [
            'paths' => [
                static::BUNDLE_DIR . 'Presentation/Resources/templates' => 'SharedKernel',
            ],
        ]);
    }

    private function configureTwigComponents(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('twig_component', [
            'defaults' => [
                'SharedKernel\\Presentation\\Twig\\Components\\' => '@SharedKernel/components/',
            ],
        ]);
    }
}
