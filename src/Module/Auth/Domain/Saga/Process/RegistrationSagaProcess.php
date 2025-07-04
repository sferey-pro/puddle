<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Saga\Process;

use App\Core\Domain\Saga\Process\AbstractSagaProcess;
use App\Core\Domain\Saga\SagaStateId;
use App\Module\SharedContext\Domain\ValueObject\Email;
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
 * - Les données métier clés (l'ID de l'utilisateur, son email).
 * - L'étape actuelle du parcours (gérée par la machine à états).
 * - L'historique des étapes déjà complétées.
 */
final class RegistrationSagaProcess extends AbstractSagaProcess
{
    public const SAGA_TYPE = 'registration';

    public string $currentState;

    public function __construct(
        SagaStateId $id,
        UserId $userId,
        Email $email,
    ) {
        parent::__construct($id);

        $this->addToContext('email', (string) $email);
        $this->addToContext('userId', (string) $userId);
    }

    public static function sagaType(): string
    {
        return self::SAGA_TYPE;
    }

    public function userId(): UserId
    {
        return UserId::fromString($this->context('userId'));
    }

    public function email(): Email
    {
        return new Email($this->context('email'));
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
