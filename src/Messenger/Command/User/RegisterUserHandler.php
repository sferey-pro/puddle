<?php

declare(strict_types=1);

namespace App\Messenger\Command\User;

use App\Entity\User;
use App\Messenger\Attribute\AsCommandHandler;
use App\Messenger\Event\UserRegistered;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommandHandler]
final class RegisterUserHandler
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private UserPasswordHasherInterface $userPasswordHasher,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $user = new User();
        $user->setEmail($command->getEmail());

        $plainPassword = $command->getPlainPassword();

        // encode the plain password
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $plainPassword));

        $this->em->persist($user);

        $event = new UserRegistered($user->getUuid());

        $this->eventBus->dispatch(
            (new Envelope($event))
                ->with(new DispatchAfterCurrentBusStamp())
        );
    }
}
