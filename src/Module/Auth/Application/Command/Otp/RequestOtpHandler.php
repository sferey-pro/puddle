<?php

namespace App\Module\Auth\Application\Command\Otp;

use App\Auth\Domain\Service\OtpGeneratorInterface;
use App\Core\Application\Command\CommandBusInterface;
use App\Module\Auth\Domain\OneTimePasswordAttempt;
use App\Module\Auth\Domain\Repository\OtpAttemptRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\OtpAttemptId;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler()]
final readonly class RequestOtpHandler
{
    public function __construct(
        private OtpGeneratorInterface $otpGenerator,
        private OtpAttemptRepositoryInterface $otpAttemptRepository,
        private CommandBusInterface $commandBus,
        private HasherInterface $hasher
    ) {}

    public function __invoke(RequestOtp $command): void
    {
        // 1. Générer le code
        $plainOtp = $this->otpGenerator->generate();

        // 2. Créer l'agrégat de tentative
        $attempt = OneTimePasswordAttempt::request(
            OtpAttemptId::generate(),
            $command->userId,
            $command->identity, // L'identité passée dans la commande
            $plainOtp,
            fn($otp) => $this->hasher->hash($otp) // Délégation du hashage
        );
        $this->otpAttemptRepository->save($attempt);

        // 3. Déclencher l'envoi de la notification
        // On crée une commande spécifique pour l'envoi de l'OTP
        $this->commandBus->dispatch(new SendOtpNotification(
            $attempt->id,
            $command->identity,
            $plainOtp
        ));
    }
}
