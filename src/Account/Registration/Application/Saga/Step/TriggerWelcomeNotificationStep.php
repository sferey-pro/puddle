<?php

declare(strict_types=1);

namespace Account\Registration\Application\Saga\Step;

use Account\Registration\Application\Command\SendWelcomeNotification;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Kernel\Application\Bus\CommandBusInterface;
use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Application\Saga\Step\Attribute\SagaStep;
use Kernel\Application\Saga\Step\SagaStepInterface;

/**
 * Étape du Saga responsable du déclenchement de l'email de bienvenue.
 */
#[SagaStep('trigger_welcome', 'registration')]
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
