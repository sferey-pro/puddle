<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Event;

use App\Core\Domain\Event\DomainEvent;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Événement levé quand un lien de connexion a été généré avec succès.
 * Il contient toutes les informations pour que le Notifier puisse envoyer l'email.
 */
final readonly class LoginLinkGenerated extends DomainEvent
{
    public function __construct(
        private UserId $aggregateId,
        private Email $email,
        private LoginLinkDetails $loginLinkDetails,
    ) {
        parent::__construct($aggregateId);
    }

    public static function eventName(): string
    {
        return 'auth.login_link.generated';
    }

    public function userId(): UserId
    {
        return $this->aggregateId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function loginLinkDetails(): LoginLinkDetails
    {
        return $this->loginLinkDetails;
    }
}
