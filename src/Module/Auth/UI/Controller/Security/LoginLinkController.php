<?php

declare(strict_types=1);

namespace App\Module\Auth\UI\Controller\Security;

use App\Module\Auth\Application\Command\Security\CreateLoginLink;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\UserLoginId;
use App\Module\Shared\Domain\ValueObject\Email;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Uid\Uuid;

final class LoginLinkController extends AbstractController
{
    public function __construct(
        private NotifierInterface $notifier,
        private LoginLinkHandlerInterface $loginLinkHandler,
        private UserRepositoryInterface $userRepository,
        private CommandBusInterface $commandBus,
    ) {
    }

    public function __invoke(
        Request $request,
    ): Response {
        if ($request->isMethod('POST')) {
            $email = new Email($request->getPayload()->get('_username'));
            $user = $this->userRepository->ofEmail($email);

            if (null === $user) {
                $this->addFlash('warning', 'Your email address not exist.');

                return $this->redirectToRoute('login');
            }

            $uuid = Uuid::v7();

            $this->commandBus->dispatch(
                new CreateLoginLink(
                    identifier: new UserLoginId(),
                    user: $user,
                )
            );

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
