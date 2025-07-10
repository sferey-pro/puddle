<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Saga\Process;

use App\Core\Domain\Saga\Process\AbstractSagaProcess;
use App\Core\Domain\Saga\SagaStateId;
use App\Module\Auth\Domain\Notification\NotificationChannel;
use App\Module\Auth\Domain\Service\IdentifierResolver;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Représente l'état et le contexte d'un "Parcours Métier d'Inscription" en cours.
 *
 * Rôle métier :
 * Chaque instance de cette classe est un "parcours" unique pour un seul utilisateur qui s'inscrit.
 * Il sert de mémoire à long terme pour le processus et contient toutes les informations
 * nécessaires pour mener le parcours à son terme, même si celui-ci se déroule sur
 * plusieurs étapes asynchrones.
 *
 * Il est responsable de conserver :
 * - L'identifiant du parcours lui-même.
 * - Les données métier clés (l'ID de l'utilisateur, son email ou phone).
 * - L'étape actuelle du parcours (gérée par la machine à états).
 * - L'historique des étapes déjà complétées.
 */
final class RegistrationSagaProcess extends AbstractSagaProcess
{
    public string $currentState;

    private function __construct(SagaStateId $id) {
        parent::__construct($id);
    }

    public static function start(
        UserId $userId,
        UserIdentity $identity,
        NotificationChannel $channel
    ): self {
        $process = new self(SagaStateId::generate());

        $process->addToContext('userId', (string) $userId);
        $process->addToContext('identity', (string) $identity->value());
        $process->addToContext('channel', (string) $channel->value);

        return $process;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->context('userId'));
    }

    public function identity(): UserIdentity
    {
        $identityResult = IdentifierResolver::resolve($this->context('identity'));

        if ($identityResult->isFailure()) {
            throw new \InvalidArgumentException($identityResult->error()->getMessage());
        }

        /** @var UserIdentity $identity */
        $identity = $identityResult->value();

        return $identity;
    }

    public function channel(): NotificationChannel
    {
        return NotificationChannel::from($this->context('channel'));
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function setCurrentState(string $currentState, array $context = []): void
    {
        $this->currentState = $currentState;
    }
}
