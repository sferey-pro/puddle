<?php

declare(strict_types=1);

namespace Authentication\Presentation\Controller;

use Account\Core\Domain\Model\Account;
use Authentication\Application\Service\PasswordlessAuthenticationService;
use Authentication\Domain\Exception\InvalidMagicLinkException;
use Authentication\Domain\Exception\InvalidOTPException;
use Authentication\Domain\Exception\TooManyAttemptsException;
use Authentication\Domain\User;
use Authentication\Infrastructure\Security\UserSecurity;
use Authentication\Presentation\Form\OTPVerificationType;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class PasswordlessController extends AbstractController
{
    public function __construct(
        private readonly PasswordlessAuthenticationService $authService,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Page de demande d'authentification passwordless (email/phone).
     * Utilise le LiveComponent PasswordlessRequestForm.
     */
    #[Route('/start', name: 'passwordless_request', methods: ['GET', 'POST'])]
    public function request(): Response
    {
        // Si déjà authentifié, rediriger
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('@Authentication/passwordless/request.html.twig');
    }

    /**
     * Page de confirmation après envoi d'email.
     */
    #[Route('/email-sent', name: 'passwordless_email_sent')]
    public function emailSent(Request $request): Response
    {
        $email = $request->query->get('email');

        if (!$email) {
            return $this->redirectToRoute('passwordless_request');
        }

        return $this->render('@Authentication/passwordless/email_sent.html.twig', [
            'email' => $email,
            'resend_delay' => 60, // secondes avant de pouvoir renvoyer
        ]);
    }

    /**
     * Route de vérification du Magic Link.
     * ⚠️ L'authentification est gérée automatiquement par MagicLinkAuthenticator !
     */
    #[Route('/verify/{token}', name: 'auth_magic_link_verify', methods: ['GET'])]
    public function verifyMagicLink(string $token): Response
    {
        // Si on arrive ici, c'est que l'authentification a réussi
        // (sinon l'authenticator aurait redirigé vers une page d'erreur)

        $this->addFlash('success', 'Welcome! You are now logged in.');

        // Récupérer la page cible ou aller au dashboard
        $targetPath = $this->getTargetPath() ?: $this->generateUrl('dashboard');

        return $this->redirect($targetPath);
    }

    /**
     * Page de saisie du code OTP.
     * L'authentification est gérée par OTPAuthenticator lors de la soumission.
     */
    #[Route('/verify-otp', name: 'passwordless_verify_otp', methods: ['GET', 'POST'])]
    public function verifyOTP(
        Request $request,
        AuthenticationUtils $authenticationUtils
    ): Response {
        // Récupérer le numéro de téléphone
        $encodedPhone = $request->query->get('phone');
        if (!$encodedPhone) {
            return $this->redirectToRoute('passwordless_request');
        }

        $phone = base64_decode($encodedPhone);

        // Créer le formulaire
        $form = $this->createForm(OTPVerificationType::class, [
            'phone' => $phone
        ]);

        // Si POST, l'OTPAuthenticator prend le relais automatiquement
        // On récupère juste l'erreur s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('@Authentication/passwordless/verify_otp.html.twig', [
            'form' => $form,
            'error' => $error,
            'phone_masked' => $this->maskPhoneNumber($phone),
            'can_resend_at' => $this->calculateResendTime($phone),
        ]);
    }

    /**
     * Renvoyer un code OTP.
     */
    #[Route('/resend-otp', name: 'passwordless_resend_otp', methods: ['POST'])]
    public function resendOTP(Request $request): Response
    {
        $phone = $request->request->get('phone');

        if (!$phone) {
            return $this->redirectToRoute('passwordless_request');
        }

        try {
            $this->authService->resendOTP($phone, $request->getClientIp());

            $this->addFlash('info', 'A new verification code has been sent.');
        } catch (TooManyAttemptsException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('passwordless_verify_otp', [
            'phone' => base64_encode($phone)
        ]);
    }

    /**
     * Récupère le chemin cible stocké en session.
     */
    private function getTargetPath(): ?string
    {
        $session = $this->container->get('request_stack')->getSession();
        return $session->get('_security.main.target_path');
    }

    /**
     * Masque le numéro de téléphone pour l'affichage.
     */
    private function maskPhoneNumber(string $phone): string
    {
        return substr($phone, 0, 3) . '****' . substr($phone, -4);
    }

    /**
     * Calcule quand l'utilisateur peut renvoyer un code.
     */
    private function calculateResendTime(string $phone): \DateTimeInterface
    {
        // Logique basée sur le rate limiting de votre domaine
        return new \DateTime('+60 seconds');
    }
}
