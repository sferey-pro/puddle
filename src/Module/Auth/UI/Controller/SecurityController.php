<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Query\QueryBusInterface;
use App\Module\Auth\Application\Command\RequestLoginLink;
use App\Module\Auth\Application\Query\FindUserByIdentifierQuery;
use App\Module\Auth\Domain\Exception\LoginLinkException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\IpAddress;
use App\Module\Auth\Infrastructure\Symfony\Security\EmailVerifier;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class SecurityController extends AbstractController
{
    public function __construct(
        private AuthenticationUtils $authenticationUtils,
        private NotifierInterface $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private UserRepositoryInterface $userRepository,
        private CommandBusInterface $commandBus,
        private QueryBusInterface $queryBus,
        private EmailVerifier $emailVerifier,
    ) {
    }

    #[Template('@Auth/registration/register.html.twig')]
    public function register(): void
    {
    }

    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('notice', 'Veuillez vous connecté pour vérifier votre adresse e-mail.');
        }

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('register');
    }

    #[Template('@Auth/security/login.html.twig')]
    public function login(): array
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

    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function requestLoginLink(Request $request): RedirectResponse
    {
        $identifier = $request->getPayload()->get('_username');

        try {
            $this->commandBus->dispatch(new RequestLoginLink($identifier, new IpAddress($request->getClientIp())));
        } catch (LoginLinkException $e) {
            $this->addFlash('warning', $e->getMessage());

            return $this->redirectToRoute('login');
        }

        // redirect a "Login link is sent!" page
        return $this->redirectToRoute('login_link_sent', ['identifier' => $identifier]);
    }

    public function loginLinkSent(Request $request)
    {
        $identifier = $request->get('identifier');

        return $this->render(
            '@Auth/security/login_link_sent.html.twig',
            ['identifier' => $identifier]
        );
    }

    #[Template('@Auth/security/process_login_link.html.twig')]
    public function check(Request $request): array
    {
        $identifier = $request->query->get('user');
        $user = $this->queryBus->ask(new FindUserByIdentifierQuery($identifier));

        // get the login link query parameters
        $expires = $request->query->get('expires');
        $hash = $request->query->get('hash');

        // and render a template with the button
        return [
            'expires' => $expires,
            'identifier' => $identifier,
            'hash' => $hash,
            'email' => (string) $user->email(),
        ];
    }
}
