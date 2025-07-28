<?php

namespace Authentication\Infrastructure\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Point d'entrée pour rediriger vers l'authentification passwordless.
 */
final class PasswordlessEntryPoint implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator
    ) {}

    public function start(Request $request, ?AuthenticationException $authException = null): RedirectResponse
    {
        // Sauvegarder l'URL cible
        $request->getSession()->set(
            '_security.main.target_path',
            $request->getUri()
        );

        // Rediriger vers la page de connexion passwordless
        return new RedirectResponse(
            $this->urlGenerator->generate('passwordless_request')
        );
    }
}
