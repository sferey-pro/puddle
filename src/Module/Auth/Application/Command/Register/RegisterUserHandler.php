<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Module\Auth\Application\Event\UserRegistered;
use App\Module\Auth\Domain\Model\User;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\Shared\Domain\ValueObject\Email;
use App\Module\Shared\Domain\ValueObject\UserId;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommandHandler]
final class RegisterUserHandler
{
    public function __construct(
        private MessageBusInterface $eventBus,
        private EventDispatcherInterface $eventDispatcher,
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $plainPassword = $command->dto->plainPassword;
        $email = $command->dto->email;

        $user = User::register(
            identifier: $command->identifier ?? UserId::generate(),
            email: new Email($email),
        );

        // encode the plain password
        $user->setHashPassword(
            new Password($this->userPasswordHasher->hashPassword($user, $plainPassword))
        );

        $this->userRepository->save($user, true);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventDispatcher->dispatch($domainEvent);
        }

        $event = new UserRegistered(identifier: $user->identifier(), email: $user->email());

        $this->eventBus->dispatch(
            (new Envelope($event))
                ->with(new DispatchAfterCurrentBusStamp())
        );
    }
}
