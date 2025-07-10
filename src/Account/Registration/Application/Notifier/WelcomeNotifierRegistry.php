<?php
declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class WelcomeNotifierRegistry
{
    /** @var array<string, WelcomeNotifierInterface> */
    private array $notifiers;

    public function __construct(
        #[AutowireIterator('app.welcome_notifier', defaultIndexMethod: 'getSupportedChannel')]
        iterable $notifiers
    ) {
        $this->notifiers = iterator_to_array($notifiers);
    }

    public function notify(UserId $userId, NotificationChannel $channel): void
    {

        $channelKey = $channel->value;

        if (!isset($this->notifiers[$channelKey])) {
            throw new \RuntimeException(sprintf('No welcome notifier found for channel "%s".', $channelKey));
        }

        $this->notifiers[$channelKey]->notify($userId);
    }
}
