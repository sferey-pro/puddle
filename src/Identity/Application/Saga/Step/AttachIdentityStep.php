<?php

declare(strict_types=1);

namespace Identity\Application\Saga\Step;

use Identity\Application\Command\AttachIdentity;
use Identity\Application\Command\CompensateIdentityAttachment;
use Kernel\Application\Bus\CommandBusInterface;
use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Application\Saga\Step\SagaStepInterface;
use Kernel\Application\Saga\Step\Attribute\SagaStep;

#[SagaStep('attach_identity', 'registration')]
final readonly class AttachIdentityStep implements SagaStepInterface
{
    public function __construct(private CommandBusInterface $commandBus)
    {
    }

    public function execute(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $this->commandBus->dispatch(
            new AttachIdentity(
                $sagaProcess->userId(),
                $sagaProcess->identifier(),
            )
        );
    }

    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $this->commandBus->dispatch(
            new CompensateIdentityAttachment(
                $sagaProcess->userId(),
                $sagaProcess->identifier()
            )
        );
    }
}
