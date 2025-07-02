<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Saga\Step;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Application\Saga\Process\SagaProcessInterface;
use App\Core\Application\Saga\Step\SagaStepInterface;
use App\Module\Auth\Domain\Event\NewUserHasRegistered;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\LoginLinkManager;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Étape du Saga responsable du déclenchement de l'email de bienvenue.
 */
final readonly class TriggerWelcomeEmailStep implements SagaStepInterface
{
    public function __construct(
        private LoginLinkManager $loginLinkManager,
        private EventBusInterface $eventBus,
        private UserRepositoryInterface $userRepository,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Déclenche la création du lien de connexion et la publication de l'événement
     * qui mènera à l'envoi de l'e-mail.
     */
    public function execute(SagaProcessInterface $sagaProcess): void
    {
        /** @var RegistrationSagaProcess $sagaProcess */
        $loginLink = $this->loginLinkManager->createForNewUser($sagaProcess->email());

        $this->em->persist($loginLink);

        $this->eventBus->publish(
            new NewUserHasRegistered(
                $sagaProcess->userId(),
                $loginLink->details(),
                $sagaProcess->email(),
            )
        );
    }

    /**
     * Action de compensation (vide).
     * On ne peut pas "annuler" un e-mail envoyé.
     */
    public function compensate(SagaProcessInterface $sagaProcess): void
    {
        // Pas de compensation possible pour cette étape.
    }
}
