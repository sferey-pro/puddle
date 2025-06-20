<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain\Event;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\Event\DomainEvent;

/**
 * Événement levé lorsque les préférences de notification de l'utilisateur sont modifiées.
 */
final readonly class UserNotificationPreferencesUpdated extends DomainEvent
{
    /**
     * @param array<string, bool> $preferences Tableau clé-valeur des préférences.
     *                                         Ex: ['email_newsletter' => true, 'sms_alerts' => false]
     */
    public function __construct(
        private UserId $userId,
        private array $preferences,
    ) {
        parent::__construct($this->userId);
    }

    public static function eventName(): string
    {
        return 'user_management.user.notification_preferences_updated';
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function preferences(): array
    {
        return $this->preferences;
    }
}
