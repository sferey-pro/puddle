<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Event\EventBusInterface;
use App\Core\Domain\Saga\SagaStateId;
use App\Core\Domain\Specification\IsUniqueSpecification;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Application\Saga\Event\RegistrationSagaStarted;
use App\Module\Auth\Domain\Exception\UserException;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * Gère le démarrage du Saga d'inscription.
 *
 * Rôle métier :
 * Ce handler est le point d'entrée technique du "Parcours d'Inscription".
 * Il valide la règle d'unicité de l'email, crée l'objet `RegistrationSagaProcess`
 * qui suivra l'état du parcours, et notifie le système (via l'événement
 * `RegistrationSagaStarted`) que la première étape peut commencer.
 */
#[AsCommandHandler]
final readonly class StartRegistrationSagaHandler
{
    public function __construct(
        #[Target('registration_saga')]
        private WorkflowInterface $workflow,
        private UserRepositoryInterface $userRepository,
        private CommandBusInterface $commandBus,
        private EntityManagerInterface $em,
        private EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(StartRegistrationSaga $command): void
    {
        $emailResult = EmailAddress::create($command->email);
        $userId = $command->userId();

        if ($emailResult->isFailure()) {
            throw new \InvalidArgumentException($emailResult->error()->getMessage());
        }

        $email = $emailResult->value();

        // Règle métier critique : l'email doit être unique pour démarrer un nouveau parcours.
        $spec = new IsUniqueSpecification($email);
        if (0 !== $this->userRepository->countBySpecification($spec)) {
            throw UserException::emailAlreadyExists($email);
        }

        $sagaProcess = new RegistrationSagaProcess(SagaStateId::generate(), $userId, $email);

        $this->workflow->getMarking($sagaProcess);

        $this->em->persist($sagaProcess);
        $this->em->flush();

        $this->eventBus->publish(
            new RegistrationSagaStarted($sagaProcess->id())
        );
    }
}
