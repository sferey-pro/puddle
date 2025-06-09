<?php

declare(strict_types=1);

use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Application\Event\MessengerEventBus;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Doctrine\Types\AbstractEnumType;
use App\Shared\Infrastructure\Service\SystemClock;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Uid\Command\GenerateUuidCommand;
use Symfony\Component\Uid\Command\InspectUuidCommand;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\Shared\\', dirname(__DIR__, 2).'/src/Shared')
        ->exclude([dirname(__DIR__, 2).'/src/Shared/Infrastructure/Symfony/Kernel.php']);

    $services->load('App\\Module\\SharedContext\\', dirname(__DIR__, 2).'/src/Module/SharedContext')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/SharedContext/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/SharedContext/Domain/ValueObjects',
        ]);

    $services->set(ClockInterface::class, SystemClock::class);

    $services
        ->instanceof(AbstractEnumType::class)
            ->tag('app.doctrine_enum_type');

    $services
        ->alias(EventBusInterface::class, 'event.bus');

    $services->set(EventBusInterface::class)
        ->class(MessengerEventBus::class);

    if ('dev' === $containerConfigurator->env()) {
        $services
            ->set(GenerateUuidCommand::class)
            ->set(InspectUuidCommand::class);
    }
};
