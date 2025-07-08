<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Saga\SagaStateId;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Application\Saga\Event\RegistrationSagaStarted;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserAccountRepositoryInterface;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;
use App\Module\SharedContext\Domain\ValueObject\Email;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Gère le démarrage du Saga d'inscription.
 *
 * Rôle métier :
 * Ce handler est le point d'entrée technique du "Parcours d'Inscription".
 * Il valide la règle d'unicité de l'email, crée l'objet `RegistrationSagaProcess`
 * qui suivra l'état du parcours, et notifie le système (via l'événement
 * `RegistrationSagaStarted`) que la première étape peut commencer.
 */
#[AsCommandHandler]
final readonly class StartRegistrationSagaHandler
{
    public function __construct(
        #[Target('registration_saga')]
        private WorkflowInterface $workflow,
        private UserAccountRepositoryInterface $useAccountRepository,
        private CommandBusInterface $commandBus,
        private EntityManagerInterface $em,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(StartRegistrationSaga $command): void
    {
        $identifier = $command->identifier;
        $userId = $command->userId();

        // Règle métier critique : l'email doit être unique pour démarrer un nouveau parcours.
        $spec = new IsUniqueSpecification($identifier->value);
        if (0 !== $this->useAccountRepository->countBySpecification($spec)) {
            throw UserException::identifierAlreadyExists($identifier);
        }

        $sagaProcess = new RegistrationSagaProcess(SagaStateId::generate(), $userId, $identifier);

        $this->workflow->getMarking($sagaProcess);

        $this->em->persist($sagaProcess);
        $this->em->flush();

        $this->eventBus->publish(
            new RegistrationSagaStarted($sagaProcess->id())
        );
    }
}
