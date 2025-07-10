<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Event\EventBusInterface;
use App\Core\Application\Saga\SagaManager;
use App\Core\Domain\Saga\SagaStateId;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Application\Saga\Event\RegistrationSagaStarted;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;
use App\Module\Auth\Domain\Service\IdentifierResolver;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
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
        private UserRepositoryInterface $userRepository,
        private CommandBusInterface $commandBus,
        private EventBusInterface $eventBus,
        private SagaManager $sagaManager,
    ) {
    }

    public function __invoke(StartRegistrationSaga $command): void
    {
        $identityResult = IdentifierResolver::resolve($command->identifier);

        if ($identityResult->isFailure()) {
            throw new \InvalidArgumentException($identityResult->error()->getMessage());
        }

        /** @var UserIdentity $identity */
        $identity = $identityResult->value();

        $userId = $command->userId;

        $spec = new IsUniqueSpecification($identity);
        if (0 !== $this->userRepository->countBySpecification($spec)) {
            throw UserException::identityAlreadyInUse($identity);
        }

        $sagaProcess = RegistrationSagaProcess::start(
            $userId,
            $identity,
            $command->channel
        );

        $this->workflow->getMarking($sagaProcess);

        $this->sagaManager->save($sagaProcess);

        $this->eventBus->publish(
            new RegistrationSagaStarted($sagaProcess->id)
        );
    }
}
