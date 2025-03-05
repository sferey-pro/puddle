<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Command\CommandBusInterface;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Messenger\Command\User\RegisterUser;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailVerifier $emailVerifier,
    ) {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        CommandBusInterface $commandBus,
    ): Response {
        $form = $this->createForm(RegistrationFormType::class, new User());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandBus->dispatch(new RegisterUser(
                email: $form->get('email')->getData(),
                plainPassword: $form->get('plainPassword')->getData(),
            ));

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            /** @var User $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
