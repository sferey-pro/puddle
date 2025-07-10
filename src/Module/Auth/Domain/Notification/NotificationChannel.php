<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Notification;

enum NotificationChannel: string
{
    case Email = 'email';
    case Sms = 'sms';
}