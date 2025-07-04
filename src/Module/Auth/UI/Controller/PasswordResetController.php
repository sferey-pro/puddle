<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller;

use App\Module\Auth\Application\Command\PasswordRequest\RequestPasswordReset;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Contrôleur pour le processus de réinitialisation de mot de passe.
 *
 * En tant qu'adaptateur de la couche UI, son unique responsabilité est de mapper
 * les routes aux bonnes vues. Toute la logique d'interaction et de traitement
 * est déléguée aux LiveComponents qu'il affiche.
 */
final class PasswordResetController extends AbstractController
{
    /**
     * Affiche la page initiale où un utilisateur peut démarrer le processus de réinitialisation.
     */
    #[Template('@Auth/reset_password/request.html.twig')]
    public function request(): array
    {
        return [];
    }

    /**
     * Affiche une page de confirmation générique après la soumission de la demande.
     * Pour des raisons de sécurité, cette page est statique et ne révèle aucune information.
     */
    #[Template('@Auth/reset_password/check_email.html.twig')]
    public function checkEmail(Request $request): array
    {
        $expirationDate = $request->get('expiresAt');

        return [
            'expirationDate' => $expirationDate ?? new \DateTimeImmutable(RequestPasswordReset::EXPIRES_AT_TIME),
        ];
    }

    /**
     * Affiche la page finale où un utilisateur peut saisir son nouveau mot de passe.
     *
     * @param string $token le token public sécurisé, extrait de l'URL, qui identifie la demande
     */
    #[Template('@Auth/reset_password/reset.html.twig')]
    public function reset(string $token): array
    {
        return [
            'token' => $token,
        ];
    }
}
