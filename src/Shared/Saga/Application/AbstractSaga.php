<?php

declare(strict_types=1);

namespace App\Shared\Saga\Application;

use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Command\CommandInterface;
use App\Shared\Saga\Domain\Repository\SagaStateRepositoryInterface;
use App\Shared\Saga\Domain\SagaState;

/**
 * Le "chef d'orchestre" abstrait d'un processus métier.
 *
 * Chaque saga concrète (ex: RegistrationSaga) héritera de cette classe pour définir
 * sa "partition" : la séquence d'actions et les plans de secours (compensations).
 * Elle fournit la mécanique de base pour interagir avec le CommandBus et persister l'état.
 *
 */
abstract class AbstractSaga
{
    public function __construct(
        protected CommandBusInterface $commandBus,
        protected SagaStateRepositoryInterface $sagaStateRepository
    ) {
    }

    /**
     * Retourne le nom unique du processus métier.
     * C'est le lien symbolique entre cette classe d'orchestration et les
     * instances de SagaState en base de données (ex: 'user_registration').
     */
    abstract public static function sagaType(): string;

    /**
     * Définit la "partition" du processus métier.
     *
     * C'est ici que l'on décrit chaque étape de la saga de manière déclarative.
     * Le tableau retourné liste les étapes dans l'ordre. Pour chaque étape, on définit :
     * - 'action': La commande à exécuter pour accomplir l'étape.
     * - 'compensation': (Optionnel) La commande à exécuter pour annuler l'action de cette étape si une étape ultérieure échoue.
     *
     * @return array<int, array{action: class-string<CommandInterface>, compensation?: class-string<CommandInterface>}>
     */
    abstract protected function stepsDefinition(): array;

    /**
     * Lit la partition (`getStepsDefinition`) et exécute la prochaine action
     * pour l'état de saga donné.
     * S'il n'y a plus d'étapes, la saga est marquée comme complétée.
     */
    protected function executeNextStep(SagaState $sagaState): void
    {
        $steps = $this->stepsDefinition();
        $currentStepConfig = $steps[$sagaState->currentStep()] ?? null;

        if ($currentStepConfig === null) {
            $sagaState->complete();
            $this->sagaStateRepository->save($sagaState);
            return;
        }

        // La logique concrète de création de la commande et de son dispatch
        // sera implémentée dans les classes filles, car elles seules connaissent
        // les arguments nécessaires pour leurs commandes.
        // Exemple:
        // $payload = $sagaState->getPayload();
        // $command = new $currentStepConfig['action']($payload['userId'], ...);
        // $this->commandBus->dispatch($command);
    }

    /**
     * Lit la partition et exécute la compensation pour chaque étape
     * déjà terminée, en ordre inverse.
     */
    protected function compensate(SagaState $sagaState): void
    {
        $steps = $this->stepsDefinition();

        // On parcourt les étapes réussies en sens inverse pour les compenser.
        for ($i = $sagaState->currentStep() - 1; $i >= 0; --$i) {
            $stepToCompensate = $steps[$i] ?? null;

            if (isset($stepToCompensate['compensation'])) {
                 // Logique pour dispatcher la commande de compensation
                 // $payload = $sagaState->getPayload();
                 // $command = new $stepToCompensate['compensation']($payload['userId']);
                 // $this->commandBus->dispatch($command);
            }
        }
    }
}
