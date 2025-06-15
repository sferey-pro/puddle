<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller\Security;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'affichage du formulaire de connexion et des erreurs d'authentification.
 *
 * Ce contrôleur utilise AuthenticationUtils pour récupérer la dernière erreur d'authentification
 * et le dernier nom d'utilisateur saisi, puis les transmet au template Twig pour affichage.
 */
final class LoginController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils L'utilitaire pour récupérer les informations d'authentification
     */
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
    ) {
    }

    /**
     * Affiche le formulaire de connexion.
     *
     * Récupère la dernière erreur d'authentification (s'il y en a une)
     * et le dernier nom d'utilisateur saisi par l'utilisateur.
     * Ces informations sont passées au template 'security/login.html.twig'.
     *
     * @return array les données à transmettre au template Twig
     */
    #[Template('@Auth/security/login.html.twig')]
    public function __invoke(): array
    {
        // get the login error if there is one
        $error = $this->authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $this->authenticationUtils->getLastUsername();

        return [
            'last_username' => $lastUsername,
            'error' => $error,
        ];
    }
}
