<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Register;

use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\Password;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommandHandler]
final class RegisterUserHandler
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepositoryInterface $userRepository,
        private EventDispatcherInterface $eventDispatcher,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RegisterUser $command): void
    {
        $plainPassword = $command->dto->plainPassword;
        $email = $command->dto->email;

        $user = UserAccount::register(
            id: $command->id ?? UserId::generate(),
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

        // $this->eventBus->publish(... $user->pullDomainEvents());
    }
}
