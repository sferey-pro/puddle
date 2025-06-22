<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class PasswordResetController extends AbstractController
{
    /**
     * Affiche la page contenant le formulaire de demande de réinitialisation.
     * La logique du formulaire est gérée par le LiveComponent.
     */
    #[Template('@Auth/reset_password/request.html.twig')]
    public function request(): array
    {
        return [];
    }

    /**
     * Page de confirmation affichée après la demande.
     */
    #[Template('@Auth/reset_password/check_email.html.twig')]
    public function checkEmail(SessionInterface $session): array
    {
        $expirationDate = $session->get('password_reset_expires_at');

        // On nettoie la session pour que l'information ne soit affichée qu'une seule fois.
        $session->remove('password_reset_expires_at');

        // Generate a fake expirationDate if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (!$expirationDate) {
            $expirationDate = new \DateTimeImmutable('+1 hour');
        }

        return [
            'expirationDate' => $expirationDate,
        ];
    }

    /**
     * Affiche la page contenant le formulaire pour saisir un nouveau mot de passe.
     * Le token est passé au LiveComponent pour qu'il puisse l'utiliser.
     */
    #[Template('@Auth/reset_password/reset.html.twig')]
    public function reset(string $token): array
    {
        return [
            'token' => $token,
        ];
    }
}
