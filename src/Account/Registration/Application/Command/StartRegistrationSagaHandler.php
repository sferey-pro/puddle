<?php

declare(strict_types=1);

namespace Account\Registration\Application\Command;

use Account\Core\Domain\Repository\AccountRepositoryInterface;
use Account\Registration\Application\Saga\Event\RegistrationSagaStarted;
use Account\Registration\Domain\Exception\RegistrationException;
use Account\Registration\Domain\Model\RegistrationRequest;
use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Account\Registration\Domain\Specification\CanRegisterSpecification;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Application\Bus\EventBusInterface;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use SharedKernel\Domain\Service\IdentityContextInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

#[AsCommandHandler]
final readonly class StartRegistrationSagaHandler
{
    public function __construct(
        #[Target('registration_saga')]
        private WorkflowInterface $workflow,
        private CanRegisterSpecification $canRegisterSpecification,
        private IdentityContextInterface $identityContext,
        private AccountRepositoryInterface $accountRepository,
        private EventBusInterface $eventBus,
        private RegistrationProcessRepositoryInterface $processRepository,
    ) {
    }

    public function __invoke(StartRegistrationSaga $command): void
    {
        $identifier = $this->identityContext->resolveIdentifierOrThrow($command->identifier);

        $existingProcess = $this->processRepository->findActiveByIdentifier($identifier);
        if ($existingProcess) {
            throw RegistrationException::alreadyInProgress($identifier);
        }

        // ===== RÈGLES MÉTIER PURES =====
        $registrationRequest = new RegistrationRequest(
            $identifier,
            $command->userId,
            ['ip_address' => $command->ipAddress]
        );

        if (!$this->canRegisterSpecification->isSatisfiedBy($registrationRequest)) {
            throw RegistrationException::canRegister($this->canRegisterSpecification->failureReason());
        }

        if ($this->identityContext->findUserIdByIdentifier($identifier)) {
            throw RegistrationException::identifierAlreadyExists($identifier);
        }

        // ===== CRÉATION DU SAGA =====
        $sagaProcess = RegistrationSagaProcess::start(
            $command->userId,
            $identifier,
        );

        $this->workflow->getMarking($sagaProcess);

        $this->processRepository->save($sagaProcess);

        $this->eventBus->publish(
            new RegistrationSagaStarted($sagaProcess->id),
        );
    }
}
