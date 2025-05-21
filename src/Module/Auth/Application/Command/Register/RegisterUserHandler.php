<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Module\Auth\Application\Event\UserRegistered;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
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
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $plainPassword = $command->dto->plainPassword;
        $email = $command->dto->email;

        $user = UserAccount::register(
            identifier: $command->identifier ?? UserId::generate(),
            email: new Email($email),
        );

        // encode the plain password
        $user->setHashPassword(
            new Password($this->userPasswordHasher->hashPassword($user, $plainPassword))
        );

        $this->userRepository->save($user, true);

        foreach ($user->pullDomainEvents() as $domainEvent) {
            $this->eventBus->dispatch(
                (new Envelope($domainEvent))
                        ->with(new DispatchAfterCurrentBusStamp())
                );
        }
    }
}
