<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Doctrine\Types\AbstractEnumType;
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

    $services->load('App\\Module\\Shared\\', dirname(__DIR__, 2).'/src/Module/Shared')
        ->exclude([
            dirname(__DIR__, 2).'/src/Module/Shared/Domain/Model',
            dirname(__DIR__, 2).'/src/Module/Shared/Domain/ValueObjects',
        ]);;

    $services
        ->set(GenerateUuidCommand::class)
        ->set(InspectUuidCommand::class);

    $services
        ->instanceof(AbstractEnumType::class)
            ->tag('app.doctrine_enum_type');
};
