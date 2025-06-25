<?php

declare(strict_types=1);

namespace Tests\Behat\Handler;

use App\Module\Auth\Application\Command\RegisterUser;
use App\Module\UserManagement\Application\Command\CreateUserHandler;
use Tests\Behat\Service\FailureSimulator;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/**
 * Ce handler est un décorateur pour le vrai CreateUserHandler.
 * Il n'est activé que dans l'environnement de test.
 *
 * Son rôle est d'intercepter la commande CreateUser pour simuler un échec
 * si le FailureSimulator le lui demande. Sinon, il délègue le travail
 * au vrai handler.
 */
final class FailurableCreateUserHandler implements MessageHandlerInterface
{
    public function __construct(
        // Le décorateur encapsule le handler original
        private readonly CreateUserHandler $decoratedHandler,
        private readonly FailureSimulator $failureSimulator
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        // On vérifie si notre simulateur doit déclencher un échec
        if ($this->failureSimulator->mustFail($command->email->value)) {
            // On lève une exception pour simuler un problème (ex: BDD indisponible)
            throw new \RuntimeException('Échec forcé par le test Behat pour la création du profil.');
        }

        // Si aucun échec n'est demandé, on appelle le vrai handler
        ($this->decoratedHandler)($command);
    }
}
