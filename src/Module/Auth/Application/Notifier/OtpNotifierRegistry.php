<?php
declare(strict_types=1);

namespace App\Module\Auth\Application\Notifier;

use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\Auth\Domain\Notification\OtpNotifierInterface;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

final readonly class OtpNotifierRegistry
{
    /** @var array<string, OtpNotifierInterface> */
    private array $notifiers;

    public function __construct(
        #[AutowireIterator('app.otp_notifier', defaultIndexMethod: 'getSupportedChannel')]
        iterable $notifiers
    ) {
        $this->notifiers = iterator_to_array($notifiers);
    }

    public function notify(UserIdentity $identity, string $plainOtpCode, NotificationChannel $channel): void
    {
        $channelKey = $channel->value;

        if (!isset($this->notifiers[$channelKey])) {
            throw new \RuntimeException(sprintf('No welcome notifier found for channel "%s".', $channelKey));
        }

        $this->notifiers[$channelKey]->notify($identity, $plainOtpCode);
    }
}
