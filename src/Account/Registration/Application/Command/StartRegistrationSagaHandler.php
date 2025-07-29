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
        $identityResult = $this->identityContext->resolveIdentifier($command->identifier);

        if ($identityResult->isFailure()) {
            throw new \InvalidArgumentException($identityResult->error->getMessage());
        }

        $identifier = $identityResult->value();
        $userId = $command->userId;

        $existingProcess = $this->processRepository->findActiveByIdentifier($identifier->getValue());

        if ($existingProcess) {
            throw RegistrationException::alreadyInProgress($identifier->getValue());
        }

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
        );

        $this->workflow->getMarking($sagaProcess);

        $this->processRepository->save($sagaProcess);

        $this->eventBus->publish(
            new RegistrationSagaStarted($sagaProcess->id),
        );
    }
}
