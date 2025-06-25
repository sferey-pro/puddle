<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\EventSubscriber;

use App\Module\Auth\Application\Command\RequestInitialPasswordSetup;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Rôle : Écoute la création d'un utilisateur dans le module UserManagement
 * et déclenche une commande vers le module Auth pour envoyer un e-mail d'invitation.
 * C'est un point de communication inter-modules crucial.
 */
#[AsEventListener(event: UserCreated::class, method: 'onUserCreated')]
final readonly class WhenUserCreatedThenSendInvitationEmail
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {}

    public function onUserCreated(UserCreated $event): void
    {
        // On ne veut envoyer cet e-mail que si l'utilisateur a été créé par un admin,
        // pas lors d'une inscription standard.
        // Pour l'instant, nous le faisons pour toute création. Une condition pourrait être ajoutée.
        // ex: if ($event->createdByAdmin()) { ... }

        $this->commandBus->dispatch(new RequestInitialPasswordSetup(
            $event->userId,
            $event->email
        ));
    }
}
