<?php

declare(strict_types=1);

namespace App\Messenger\EventSubscriber;

use App\Entity\User;
use App\Messenger\Event\UserRegistered;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Address;

#[AsMessageHandler]
class WhenUserRegisteredThenSendUserConfirmationEmail
{
    public function __construct(
        private EmailVerifier $emailVerifier,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(UserRegistered $event): void
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(
            ['uuid' => $event->getUuid()]
        );

        // generate a signed url and email it to the user
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@puddle.com', 'Puddle Mail Bot'))
                ->to((string) $user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('emails/registration/confirmation_email.html.twig')
        );
    }
}
