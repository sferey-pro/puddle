<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Symfony\Security\Authentication;

use App\Module\Auth\Application\Command\RecordLoginLinkFailure;
use App\Module\Auth\Application\Query\FindUserByIdentifierQuery;
use App\Module\Auth\Domain\UserAccount;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Signature\Exception\ExpiredSignatureException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\LoginLink\Exception\ExpiredLoginLinkException;
use Symfony\Component\Security\Http\LoginLink\Exception\InvalidLoginLinkException;

final class AuthenticationLoginLinkFailureHandler implements AuthenticationFailureHandlerInterface
{
    protected array $defaultOptions = [
        'failure_path' => 'login',
    ];

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly HttpUtils $httpUtils,
    ) {
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $option = $this->defaultOptions;

        // On récupère la cause première de l'échec.
        $rootCause = $this->getRootCause($exception);

        // On vérifie si la cause racine est une exception d'expiration.
        // Symfony peut lever l'une ou l'autre selon le contexte.
        if ($rootCause instanceof ExpiredLoginLinkException || $rootCause instanceof ExpiredSignatureException) {
            $message = 'Ce lien de connexion a expiré. Veuillez en demander un nouveau.';
        } else {
            // Message générique pour toutes les autres erreurs (hash invalide, utilisateur inconnu...).
            $message = 'Le lien de connexion est invalide ou a déjà été utilisé.';
        }

        $request->getSession()->getFlashBag()->add('warning', $message);

        // On tente d'enregistrer l'échec, si possible.
        if ($exception instanceof InvalidLoginLinkException) {
            $this->recordFailure($exception);
        }

        return $this->httpUtils->createRedirectResponse($request, $option['failure_path']);
    }

    /**
     * Tente d'identifier l'utilisateur associé à l'échec et déclenche la commande
     * pour enregistrer cette tentative.
     */
    private function recordFailure(InvalidLoginLinkException $exception): void
    {
        $loginLinkDetails = $exception->getLoginLinkDetails();

        /** @var ?UserAccount $user */
        $user = $this->queryBus->ask(new FindUserByIdentifierQuery($loginLinkDetails->getUserIdentifier()));

        if (null !== $user) {
            $this->commandBus->dispatch(new RecordLoginLinkFailure($user->id()));
        }
    }

    /**
     * Parcourt la chaîne des exceptions pour trouver la cause originelle (la racine).
     * C'est la méthode la plus propre pour identifier le véritable déclencheur.
     */
    private function getRootCause(\Throwable $exception): \Throwable
    {
        $rootCause = $exception;

        while ($rootCause->getPrevious()) {
            $rootCause = $rootCause->getPrevious();
        }

        return $rootCause;
    }
}
