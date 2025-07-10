<?php
declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Otp;

use App\Core\Application\Command\CommandBusInterface;
// Le Repository doit pouvoir trouver la dernière tentative pour une identité donnée
use App\Module\Auth\Domain\Repository\OtpAttemptRepositoryInterface;
// Un service pour hacher et vérifier, qui encapsule l'algorithme (sha256+salt, etc.)
use App\Module\Auth\Domain\Service\OtpHasherInterface;
// La commande pour finaliser la connexion
use App\Module\Auth\Application\Command\AuthenticateUserCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class VerifyOtpHandler
{
    public function __construct(
        private OtpAttemptRepositoryInterface $otpAttemptRepository,
        private OtpHasherInterface $otpHasher,
        private CommandBusInterface $commandBus
    ) {
    }

    public function __invoke(VerifyOtpCommand $command): void
    {
        // 1. Retrouver la tentative en cours pour cette identité
        $attempt = $this->otpAttemptRepository->findLastPendingForIdentity($command->identity);

        if ($attempt === null) {
            // Gérer le cas où aucune tentative n'a été demandée
            throw new \RuntimeException('No OTP request found for this identity.');
        }

        // 2. Déléguer la logique de vérification à l'agrégat
        // C'est lui le gardien des règles (expiration, tentatives, etc.)
        $verifier = fn(string $plain, string $hash): bool => $this->otpHasher->verify($plain, $hash);

        try {
            $attempt->verify($command->submittedOtpCode, $verifier);
        } catch (OtpVerificationException $e) {
            // Enregistrer l'échec et relancer pour que l'UI réagisse
            $this->otpAttemptRepository->save($attempt);
            throw $e;
        }

        // 3. Succès !
        $this->otpAttemptRepository->save($attempt);

        // 4. L'utilisateur est maintenant authentifié. On déclenche la suite.
        // Cela pourrait créer une session, un token JWT, etc.
        $this->commandBus->dispatch(new AuthenticateUserCommand(
            $attempt->userUserId()
        ));
    }
}
