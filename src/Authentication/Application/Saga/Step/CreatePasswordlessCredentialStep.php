<?php

namespace Authentication\Application\Saga\Step;

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Authentication\Domain\Model\AccessCredential\MagicLinkCredential;
use Authentication\Domain\Model\AccessCredential\OTPCredential;
use Authentication\Domain\Repository\AccessCredentialRepositoryInterface;
use Authentication\Domain\Service\TokenGeneratorInterface;
use Authentication\Infrastructure\Security\SymfonyLoginLinkAdapter;
use Kernel\Application\Saga\Process\SagaProcessInterface;
use Kernel\Application\Saga\Step\Attribute\SagaStep;
use Kernel\Application\Saga\Step\SagaStepInterface;


#[SagaStep('attach_identity', 'registration')]
final class CreatePasswordlessCredentialStep implements SagaStepInterface
{
    public function __construct(
        private readonly AccessCredentialRepositoryInterface $credentialRepository,
        private readonly TokenGeneratorInterface $tokenGenerator,
        private readonly SymfonyLoginLinkAdapter $loginLinkAdapter
    ) {}

    public function execute(SagaProcessInterface $sagaProcess): void
    {
        
    }

    public function compensate(SagaProcessInterface $sagaProcess): void
    {

    }
}
