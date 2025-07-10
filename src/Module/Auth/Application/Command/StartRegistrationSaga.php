<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Commande pour démarrer le Saga d'inscription.
 *
 * Rôle métier :
 * Cette commande représente l'intention de démarrer le "Parcours métier d'Inscription".
 * Elle embarque les données initiales et génère un ID unique qui sera utilisé
 * pour suivre ce parcours spécifique à travers toutes ses étapes.
 */
final readonly class StartRegistrationSaga implements CommandInterface
{
    private(set) UserId $userId;
    private(set) NotificationChannel $channel;

    public function __construct(
        public string $identifier,
        public bool $agreeTerms,
    ) {
        $this->userId = UserId::generate();

        $this->channel = self::determineChannel($identifier);
    }

    private static function determineChannel(string $identifier): NotificationChannel
    {
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return NotificationChannel::Email;
        }

        return NotificationChannel::Sms;
    }
}
