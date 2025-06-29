<?php

declare(strict_types=1);

namespace App;

use App\Core\Application\Clock\SystemTime;
use App\Core\Infrastructure\Clock\SystemClock;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function __construct(string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        SystemTime::setDefaultClockProvider(static function (): SystemClock {
            return new SystemClock();
        });
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import(\sprintf('%s/config/{packages}/*.{php,yaml}', $this->getProjectDir()));
        $container->import(\sprintf('%s/config/{packages}/%s/*.{php,yaml}', $this->getProjectDir(), $this->environment));

        $container->import(\sprintf('%s/config/{services}/*.{php,yaml}', $this->getProjectDir()));
        $container->import(\sprintf('%s/config/{services}/%s/*.{php,yaml}', $this->getProjectDir(), $this->environment));

        $container->import(\sprintf('%s/config/services.yaml', $this->getProjectDir()));
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(\sprintf('%s/config/{routes}/%s/*.php', $this->getProjectDir(), $this->environment));
        $routes->import(\sprintf('%s/config/{routes}/*.php', $this->getProjectDir()));
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->registerAttributeForAutoconfiguration(AsQueryHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'query.bus']);
        });

        $container->registerAttributeForAutoconfiguration(AsCommandHandler::class, static function (ChildDefinition $definition): void {
            $definition->addTag('messenger.message_handler', ['bus' => 'command.bus']);
        });

        // $container->registerAttributeForAutoconfiguration(AsEventHandler::class, static function (ChildDefinition $definition): void {
        //     $definition->addTag('messenger.message_handler', ['bus' => 'event.bus']);
        // });
    }
}
