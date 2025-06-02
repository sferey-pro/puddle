<?php

declare(strict_types=1);

use App\Module\Music\Application\Command\FetchCurrentlyPlayingMusicCommand;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Application\Query\QueryInterface;
use App\Shared\Domain\Event\DomainEventInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (ContainerConfigurator $containerConfigurator,FrameworkConfig $framework): void {
    $messenger = $framework->messenger();

    $messenger->defaultBus('command.bus');
    $commandBus = $messenger->bus('command.bus');

    if ('test' === $containerConfigurator->env()) {
        $commandBus->middleware()->id('doctrine_transaction');
    }

    $queryBus = $messenger->bus('query.bus');

    $eventBus = $framework->messenger()->bus('event.bus');
    $eventBus->defaultMiddleware()
        ->enabled(true)
        ->allowNoHandlers(false)
        ->allowNoSenders(true)
    ;

    $messenger->transport('sync')
        ->dsn('sync://');

    $messenger->routing(QueryInterface::class)->senders(['sync']);
    $messenger->routing(CommandInterface::class)->senders(['sync']);
    $messenger->routing(DomainEventInterface::class)->senders(['sync']);

    $messenger->failureTransport('failed_default');

    $messenger->transport('async_priority_high')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->options(['queue_name' => 'high'])
        ->failureTransport('failed_high_priority');

    $messenger->transport('async_priority_low')
        ->dsn('doctrine://default?queue_name=async_priority_low');

   $messenger->transport('failed_default')
        ->dsn('doctrine://default?queue_name=failed_default');

   $messenger->transport('failed_high_priority')
        ->dsn('doctrine://default?queue_name=failed_high_priority');

    $messenger->routing(FetchCurrentlyPlayingMusicCommand::class)
        ->senders(['async_priority_low']);
};
