<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Listener;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\UserManagement\Application\Command\CreateUser;
use App\Module\UserManagement\Application\DTO\CreateUserDTO;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * ACL : Anti-Corruption Layer.
 *
 * Cet écouteur (Handler) agit comme une couche anti-corruption. Il écoute un événement
 * provenant du Bounded Context 'Auth' (UserRegistered) et le traduit en une commande
 * compréhensible par le Bounded Context local 'UserManagement' (RegisterUser).
 *
 * Il protège ainsi UserManagement de la connaissance directe du modèle interne de Auth.
 */
#[AsMessageHandler()]
class CreateUserOnUserRegisteredHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * Gère l'événement UserRegistered du contexte Auth.
     */
    public function __invoke(UserRegistered $event): void
    {
        // 1. On reçoit l'événement du contexte externe 'Auth'.
        // L'événement contient les données nécessaires : ID et email.
        $userId = $event->userId();
        $email = $event->email();

        // 2. Création du DTO à partir des données de l'événement.
        $dto = new CreateUserDTO(
            email: (string) $email
        );

        // 3. Validation explicite du DTO.
        $violations = $this->validator->validate($dto);

        if (\count($violations) > 0) {
            // 4. Que faire en cas d'erreur ?
            // Une violation ici est grave : cela signifie que le contexte Auth a envoyé
            // des données que UserManagement juge invalides.
            // On lève une exception pour que l'événement échoue et soit potentiellement
            // traité de nouveau ou placé dans une file d'attente d'échecs.
            throw new ValidationFailedException($dto, $violations);
        }

        // 2. On TRADUIT cet événement en une COMMANDE locale.
        // On crée une commande 'CreateUser' qui appartient au contexte 'UserManagement'.
        $command = new CreateUser($dto, $userId);

        // 3. On distribue la commande sur le bus de commandes local.
        $this->commandBus->dispatch($command);
    }
}
