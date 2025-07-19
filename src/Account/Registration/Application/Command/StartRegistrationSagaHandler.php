<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Registration\Application\Saga\Event\RegistrationSagaStarted;
use Account\Registration\Domain\Exception\RegistrationException;
use Account\Registration\Domain\Model\RegistrationRequest;
use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Account\Registration\Domain\Service\IdentifierResolverInterface;
use Account\Registration\Domain\Specification\CanRegisterSpecification;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Bus\CommandBusInterface;
use Kernel\Application\Bus\EventBusInterface;
use Kernel\Application\Bus\QueryBusInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use SharedKernel\Domain\Service\IdentityContextInterface;
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
        private CanRegisterSpecification $canRegisterSpecification,
        private IdentityContextInterface $identityContext,
        private AccountRepositoryInterface $accountRepository,
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus,
        private EventBusInterface $eventBus,
        private RegistrationProcessRepositoryInterface $processRepository,
    ) {
    }

    public function __invoke(StartRegistrationSaga $command): void
    {
        $identityResult = $this->identityContext->resolveIdentifier($command->identifier);

        if ($identityResult->isFailure()) {
            throw new \InvalidArgumentException($identityResult->error->getMessage());
        }

        $identifier = $identityResult->value();
        $userId = $command->userId;

        $registrationRequest = new RegistrationRequest(
            $identifier,
            $userId,
            ['ip_address' => $command->ipAddress]
        );

        if (!$this->canRegisterSpecification->isSatisfiedBy($registrationRequest)) {
            throw RegistrationException::canRegister($this->canRegisterSpecification->failureReason());
        }

        $sagaProcess = RegistrationSagaProcess::start(
            $command->userId,
            $identifier,
            $channel,
        );

        $this->workflow->getMarking($sagaProcess);

        $this->processRepository->save($sagaProcess);

        $this->eventBus->publish(
            new RegistrationSagaStarted($sagaProcess->id),
        );
    }
}
