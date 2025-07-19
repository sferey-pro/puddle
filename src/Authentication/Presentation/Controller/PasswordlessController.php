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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class PasswordlessController extends AbstractController
{
    use TargetPathTrait;

    public function __construct(
        private readonly UserAuthenticatorInterface $userAuthenticator,
        private readonly AuthenticatorInterface $authenticator,
        private readonly PasswordlessAuthenticationService $authService
    ) {

    }

    /**
     * Formulaire unique email/phone
     */
    #[Route('/start', name: 'passwordless_request', methods: ['GET', 'POST'])]
    public function request(
        #[CurrentUser()] ?UserSecurity $user,
        Request $request
    ): Response
    {
        // Si déjà connecté, rediriger
        if ($user) {
            return $this->redirectToRoute('dashboard');
        }

        return $this->render('@Authentication/passwordless/request.html.twig');
    }

    /**
     * Page de confirmation email envoyé
     */
    #[Route('/account/register/email-sent', name: 'passwordless_email_sent')]
    public function emailSent(Request $request): Response
    {
        $email = $request->query->get('email');

        return $this->render('@Authentication/passwordless/email_sent.html.twig', [
            'email' => $email,
        ]);
    }

    /**
     * Vérification du magic link
     */
    #[Route('/auth/magic-link/{token}', name: 'auth_magic_link_verify', methods: ['GET'])]
    public function verifyMagicLink(string $token, Request $request): Response
    {
        try {
            $account = $this->authService->verifyMagicLink(
                token: $token,
                ipAddress: $request->getClientIp(),
                userAgent: $request->headers->get('User-Agent')
            );

            // Authentifier l'utilisateur avec Symfony Security
            $this->authenticateUser($account);

            $this->addFlash('success', 'Welcome! You are now logged in.');

            // Rediriger vers la page demandée ou dashboard
            $targetPath = $this->getTargetPath($request->getSession(), 'main');
            return $this->redirect($targetPath ?: $this->generateUrl('dashboard'));

        } catch (InvalidMagicLinkException $e) {
            $this->addFlash('error', 'This link is invalid or has expired.');
            return $this->redirectToRoute('passwordless_request');
        }
    }

    /**
     * Page de saisie OTP
     */
    #[Route('/account/register/verify-otp', name: 'passwordless_verify_otp', methods: ['GET', 'POST'])]
    public function verifyOTP(Request $request): Response
    {
        $encodedPhone = $request->query->get('phone');
        if (!$encodedPhone) {
            return $this->redirectToRoute('passwordless_request');
        }

        $phone = base64_decode($encodedPhone);

        $form = $this->createForm(OTPVerificationType::class, [
            'phone' => $phone
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $account = $this->authService->verifyOTP(
                    phoneNumber: $data['phone'],
                    code: $data['code'],
                    ipAddress: $request->getClientIp(),
                    userAgent: $request->headers->get('User-Agent')
                );

                // Authentifier
                $this->authenticateUser($account);

                $this->addFlash('success', 'Welcome! You are now logged in.');
                return $this->redirectToRoute('dashboard');

            } catch (InvalidOTPException $e) {
                $this->addFlash('error', 'Invalid code. Please try again.');
            } catch (TooManyAttemptsException $e) {
                $this->addFlash('error', 'Too many attempts. Please request a new code.');
                return $this->redirectToRoute('passwordless_request');
            }
        }

        return $this->render('@Authentication/passwordless/verify_otp.html.twig', [
            'form' => $form,
            'phone' => $this->obfuscatePhone($phone),
        ]);
    }

    /**
     * Resend OTP
     */
    #[Route('/account/register/resend-otp', name: 'passwordless_resend_otp', methods: ['POST'])]
    public function resendOTP(Request $request): Response
    {
        $phone = $request->request->get('phone');

        if (!$phone) {
            return $this->json(['error' => 'Phone number required'], 400);
        }

        try {
            $this->authService->initiatePasswordlessAuthentication(
                identifier: $phone,
                ipAddress: $request->getClientIp(),
                userAgent: $request->headers->get('User-Agent')
            );

            return $this->json(['success' => true, 'message' => 'New code sent']);

        } catch (TooManyAttemptsException $e) {
            return $this->json(['error' => $e->getMessage()], 429);
        }
    }

    /**
     * Helper pour authentifier via Symfony Security
     */
    private function authenticateUser(Account $account, Request $request): void
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        // Utiliser le UserAuthenticator de Symfony
        $this->userAuthenticator->authenticateUser(
            new UserSecurity(User::fromAccount($account)),
            $this->authenticator,
            $request
        );
    }

    private function obfuscatePhone(string $phone): string
    {
        // +33 6** *** **89
        return substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 6) . substr($phone, -2);
    }
}
