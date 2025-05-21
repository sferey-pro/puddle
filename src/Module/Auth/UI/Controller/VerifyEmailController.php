<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller;

use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Infrastructure\Symfony\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Contrôleur gérant le processus de vérification de l'adresse e-mail.
 *
 * Ce contrôleur est responsable de la validation du lien de vérification envoyé par e-mail
 * et de la mise à jour de l'état de vérification de l'utilisateur.
 * Nécessite que l'utilisateur soit entièrement authentifié.
 */
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class VerifyEmailController extends AbstractController
{
    /**
     * @param EmailVerifier $emailVerifier Le service pour gérer la vérification de l'e-mail.
     */
    public function __construct(
        private EmailVerifier $emailVerifier,
    ) {
    }

    /**
     * Gère la requête de vérification de l'e-mail.
     *
     * Valide le lien de vérification contenu dans la requête. En cas de succès,
     * met à jour l'utilisateur et ajoute un message flash. En cas d'échec,
     * ajoute un message flash d'erreur et redirige.
     *
     * @param Request $request La requête HTTP entrante contenant les paramètres de vérification.
     * @return RedirectResponse
     */
    public function __invoke(
        Request $request,
    ): RedirectResponse  {
        try {
            /** @var UserAccount $user */
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('homepage');
    }
}
