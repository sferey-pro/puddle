<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Listener;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Application\Command\CreateAssociatedUserAccount;
use App\Module\UserManagement\Domain\Event\UserCreated;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ACL : Anti-Corruption Layer.
 *
 * Cet écouteur (Handler) agit comme une couche anti-corruption. Il écoute un événement
 * provenant du Bounded Context 'UserManagement' (UserCreated) et le traduit en une commande
 * compréhensible par le Bounded Context local 'Auth' (RegisterUser).
 *
 * Il protège ainsi Auth de la connaissance directe du modèle interne de UserManagement.
 */
#[AsMessageHandler()]
class AssociateUserOnUserRegisteredHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * Gère l'événement UserCreated du contexte UserManagement.
     */
    public function __invoke(UserCreated $event): void
    {
        // 1. On reçoit l'événement du contexte externe 'Auth'.
        // L'événement contient les données nécessaires : ID et email.
        $userId = $event->userId();
        $email = $event->email();

        // 2. On TRADUIT cet événement en une COMMANDE locale.
        // On crée une commande 'CreateAssociatedUserAccount' qui appartient au contexte 'Auth'.
        $command = new CreateAssociatedUserAccount($userId, $email);

        // 3. On distribue la commande sur le bus de commandes local.
        $this->commandBus->dispatch($command);
    }
}
