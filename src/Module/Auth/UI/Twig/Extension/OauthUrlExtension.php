<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Twig\Extension;

use App\Module\Auth\Domain\Enum\SocialNetwork;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension Twig pour générer les URLs de connexion OAuth.
 *
 * Cette extension fournit une fonction Twig `oauthUrl` qui permet de construire
 * l'URL nécessaire pour initier une connexion via un fournisseur OAuth spécifié.
 */
class OauthUrlExtension extends AbstractExtension
{
    /**
     * @param UrlGeneratorInterface $generator Le générateur d'URL de Symfony pour créer les routes.
     */
    public function __construct(
        private UrlGeneratorInterface $generator,
    ) {
    }

    /**
     * Retourne la liste des fonctions fournies par cette extension.
     *
     * @return TwigFunction[] Un tableau contenant la fonction `oauthUrl`.
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('oauthUrl', [$this, 'generateOauthUrl']),
        ];
    }

    /**
     * Génère l'URL pour la connexion OAuth avec le réseau social spécifié.
     *
     * @param SocialNetwork $socialNetwork Le réseau social pour lequel générer l'URL de connexion.
     * @return string L'URL de connexion OAuth générée.
     */
    public function generateOauthUrl(SocialNetwork $socialNetwork): string
    {
        return $this->generator->generate('security_oauth_connect', ['socialNetwork' => $socialNetwork->value]);
    }
}
