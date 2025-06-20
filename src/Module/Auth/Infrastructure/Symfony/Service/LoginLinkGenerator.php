<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Service;

use App\Module\Auth\Domain\Service\LoginLinkGeneratorInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Hash;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

/**
 * Adaptateur d'infrastructure qui implémente notre port de génération de "magic link".
 *
 * Cette classe est un "Adapteur" au sens de l'Architecture Hexagonale. Son rôle est de
 * faire le pont entre le besoin de notre domaine (générer les détails d'un lien de connexion)
 * et une technologie externe (le composant LoginLink de Symfony).
 *
 * Il prend la demande de création, la délègue au service de Symfony, puis
 * transforme la réponse de Symfony (un objet LoginLinkDetails de Symfony) en notre
 * propre Value Object de domaine (App\Module\Auth\Domain\ValueObject\LoginLinkDetails),
 * que le reste de notre application peut comprendre et utiliser sans connaître Symfony.
 */
final readonly class LoginLinkGenerator implements LoginLinkGeneratorInterface
{
    public function __construct(
        private LoginLinkHandlerInterface $loginLinkHandler,
    ) {
    }

    /**
     * Génère les détails d'un lien de connexion en utilisant le service de Symfony.
     *
     * @param UserAccount $user L'utilisateur pour qui générer le lien
     *
     * @return LoginLinkDetails notre Value Object de domaine, prêt à être utilisé par l'application
     */
    public function generate(UserAccount $user): LoginLinkDetails
    {
        // 1. Délégation de la création du lien au composant Symfony.
        $symfonyLoginLinkDetails = $this->loginLinkHandler->createLoginLink($user);

        // 2. Extraction des informations depuis l'URL générée par Symfony.
        $request = Request::create($symfonyLoginLinkDetails->getUrl());

        // 3. Traduction (mapping) des données de Symfony vers notre Value Object de domaine.
        return new LoginLinkDetails(
            hash: new Hash($request->get('hash')), // Le hash est dans l'url.
            expiresAt: $symfonyLoginLinkDetails->getExpiresAt(),
            url: $symfonyLoginLinkDetails->getUrl()
        );
    }
}
