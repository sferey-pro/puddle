<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\EventSubscriber;

use App\Module\Auth\Domain\Event\UserRegistered;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Infrastructure\Symfony\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class WhenUserRegisteredThenSendUserConfirmationEmail
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(UserRegistered $event): void
    {
        $user = $this->userRepository->ofId($event->id());

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@puddle.com', 'Puddle Mail Bot'))
                ->to((string) $event->email())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('@Auth/emails/registration/confirmation_email.html.twig')
        );
    }
}
