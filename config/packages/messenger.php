<?php

declare(strict_types=1);

use App\Module\Music\Application\Command\FetchCurrentlyPlayingMusicCommand;
use App\Core\Application\Command\CommandInterface;
use App\Core\Application\Query\QueryInterface;
use App\Core\Domain\Event\DomainEventInterface;
use Symfony\Config\FrameworkConfig;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return static function (FrameworkConfig $framework): void {
    $messenger = $framework->messenger();

    $messenger->defaultBus('command.bus');
    $messenger->bus('command.bus')
        ->middleware()
        ->id('validation');

    $messenger->bus('query.bus')
        ->middleware()
        ->id('validation');

    $messenger->bus('event.bus')
        ->defaultMiddleware()
        ->enabled(true)
        ->allowNoHandlers(true)
        ->allowNoSenders(true)
    ;

    $messenger->transport('sync')
        ->dsn('sync://');

    $messenger->failureTransport('failed_default');

    $messenger->transport('async_priority_high')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->failureTransport('failed_high_priority');

    $messenger->transport('async_events')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'))
        ->retryStrategy()
            ->maxRetries(3)
            ->delay(1000)
            ->multiplier(2);

    $messenger->transport('async_priority_low')
        ->dsn(env('MESSENGER_TRANSPORT_DSN'));

    $messenger->transport('failed_default')
        ->dsn('doctrine://default?queue_name=failed_default');

    $messenger->transport('failed_high_priority')
        ->dsn('doctrine://default?queue_name=failed_high_priority');

    $messenger->routing(QueryInterface::class)->senders(['sync']);
    $messenger->routing(CommandInterface::class)->senders(['sync']);
    $messenger->routing(DomainEventInterface::class)->senders(['async_events']);

    $messenger->routing(FetchCurrentlyPlayingMusicCommand::class)
        ->senders(['async_priority_low']);
};
