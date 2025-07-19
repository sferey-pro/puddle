<?php

declare(strict_types=1);

namespace Account\Core\Domain\Notification;

enum NotificationChannel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}
