<?php

namespace App\Module\Auth\Application\VerifyPhoneOtp;

use App\Module\Auth\Domain\PhoneVerification\PhoneVerificationRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class VerifyPhoneOtpHandler
{
    public function __construct(
        private PhoneVerificationRepository $repository
    ) {}

    public function __invoke(VerifyPhoneOtpCommand $command): void
    {
        $verification = $this->repository->find($command->verificationId);
        // ... (gestion de l'erreur si non trouvé)

        // On définit le callable qui sera utilisé pour la vérification sécurisée.
        $verifier = fn(string $plain, string $hash): bool => hash_equals($hash, password_hash($plain, PASSWORD_DEFAULT, ['cost' => 4])); // Utiliser un algorithme rapide comme bcrypt avec un coût faible ou un hachage direct comme sha256. L'important est la comparaison constante.

        // Un hash simple est plus approprié ici qu'un password_hash car l'OTP est court et non un mot de passe.
        // On pourrait utiliser :
        $otpHasher = fn(string $plain): string => hash('sha256', $plain . $salt); // $salt serait lié à la tentative de vérification.
        $otpVerifier = fn(string $plain, string $hash): bool => hash_equals($hash, hash('sha256', $plain . $salt));


        // La méthode verify de l'agrégat est appelée avec la logique de vérification.
        $verification->verify($command->submittedOtp, $otpVerifier);

        // Si aucune exception n'est levée, c'est un succès.
        $this->repository->save($verification);

        // ... Déclencher la prochaine étape : créer le UserAccount, connecter l'utilisateur, etc.
    }
}
