<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga\Step;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Saga\Process\SagaProcessInterface;
use App\Core\Application\Saga\Step\SagaStepInterface;
use App\Module\Auth\Application\Command\Register\SendWelcomeNotification;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;

/**
 * Étape du Saga responsable du déclenchement de l'email de bienvenue.
 */
final readonly class TriggerWelcomeNotificationStep implements SagaStepInterface
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
    }

    /**
     * Déclenche la création du lien de connexion et la publication de l'événement
     * qui mènera à l'envoi de l'e-mail.
     */
    public function execute(SagaProcessInterface $sagaProcess): void
    {
        if (!$sagaProcess instanceof RegistrationSagaProcess) {
            throw new \LogicException('Cette étape ne peut être exécutée que pour une RegistrationSagaProcess.');
        }

        $this->commandBus->dispatch(
            new SendWelcomeNotification(
                $sagaProcess->userId(),
                $sagaProcess->channel(),
            )
        );
    }

    /**
     * Action de compensation (vide).
     * On ne peut pas "annuler" un e-mail envoyé.
     */
    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        // Pas de compensation possible pour cette étape.
    }
}
