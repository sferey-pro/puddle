<?php

declare(strict_types=1);

namespace App\Controller\Security;

use App\Common\Command\CommandBusInterface;
use App\Messenger\Command\Security\CreateLoginLink;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Uid\Uuid;

#[Route('/login/link', name: 'app_login_link')]
final class LoginLinkController extends AbstractController
{
    public function __construct(
        private NotifierInterface $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private UserRepository $userRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->getPayload()->get('_username');
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (null === $user) {
                $this->addFlash('warning', 'Your email address not exist.');

                return $this->redirectToRoute('app_login');
            }

            $uuid = Uuid::v7();

            $this->commandBus->dispatch(new CreateLoginLink(
                identifier: $uuid,
                user: $user,
            ));

            // render a "Login link is sent!" page
            return $this->render('security/login_link_sent.html.twig', [
                'identifier' => $uuid,
                'email' => $email,
            ]);
        }

        // if it's not submitted, render the form to request the "login link"
        return $this->render('security/request_login_link.html.twig');
    }
}
