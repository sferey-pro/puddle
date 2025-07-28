<?php

declare(strict_types=1);

namespace Kernel\Domain\Port;

/**
 * Message de notification générique
 */
final readonly class NotificationMessage
{
    public function __construct(
        private(set) string $recipient,
        private(set) string $channel, // email, sms
        private(set) string $template,
        private(set) array $parameters = []
    ) {}

}
