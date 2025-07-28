<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Saga\Process;

use Account\Core\Domain\Notification\NotificationChannel;
use DateTimeImmutable;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Saga\Process\AbstractSagaProcess;
use Kernel\Domain\Saga\SagaStateId;

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

    public function setCreatedAt(DateTimeImmutable $createdAt): void { }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void { }

    public static function start(
        UserId $userId,
        Identifier $identifier
    ): self {
        $process = self::create();

        $process->addToContext('userId', (string) $userId);
        $process->addToContext('identifier_value', $identifier->value());
        $process->addToContext('identifier_class', $identifier::class);

        return $process;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->context('userId'));
    }

    public function identifier(): Identifier
    {
        $identifierClass = $this->context('identifier_class');
        $identifierValue = $this->context('identifier_value');

        // Vérification de sécurité : s'assurer que la classe est bien un sous-type de Identifier.
        if (!is_subclass_of($identifierClass, Identifier::class)) {
            throw new \LogicException(sprintf('Cannot reconstruct identifier: class %s is not a valid Identifier.', $identifierClass));
        }

        return new $identifierClass($identifierValue);
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
