<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Saga;

use App\Core\Saga\Process\AbstractSagaProcess;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\EventId;

/**
 *
 * @see \App\Module\Auth\Infrastructure\Doctrine\Mapping\Saga.RegisterUserSagaProcess.orm.xml
 *
 * @codeCoverageIgnore
 */
final class RegisterUserSagaProcess extends AbstractSagaProcess
{
    /**
     * Rôle : Représente et persiste l'état d'une instance de la Saga d'inscription.
     * Ce processus est géré par un State Machine (Workflow) pour orchestrer les différentes étapes
     * de la création d'un utilisateur à travers les modules Auth et UserManagement.
     */
    public function __construct(
        EventId $id,
        public readonly UserId $userId,
        public readonly Email $email
    ) {
        parent::__construct($id);
    }
}
