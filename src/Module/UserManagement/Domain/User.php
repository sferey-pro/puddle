<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Core\Application\Clock\SystemTime;
use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Enum\UserStatus;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\Event\UserEmailChanged;

/**
 * Agrégat représentant un utilisateur dans le contexte de UserManagement.
 *
 * Responsabilités:
 * - Encapsuler les informations de profil
 * - Gérer le cycle de vie de l'utilisateur (statut actif, suspendu...).
 * - Garantir l'intégrité de ses propres données (invariants).
 */
final class User extends AggregateRoot
{
    use DomainEventTrait;

    private EmailAddress $email;
    private UserStatus $status;
    private readonly \DateTimeImmutable $registeredAt;

    private function __construct(
        private readonly UserId $id,
        EmailAddress $email,
    ) {
        $this->email = $email;
        $this->registeredAt = SystemTime::now();
    }

    /**
     * Crée un utilisateur directement, par exemple par un administrateur.
     * L'ID est généré car ce contexte est l'autorité.
     */
    public static function create(
        UserId $id,
        EmailAddress $email,
    ): self {
        $user = new self($id, $email);
        $user->status = UserStatus::PENDING;

        $user->recordDomainEvent(
            new UserCreated($user->id(), $user->email())
        );

        return $user;
    }

    public function changeEmail(EmailAddress $newEmail): void
    {
        if ($this->email->equals($newEmail)) {
            return;
        }

        $oldEmail = $this->email;
        $this->email = $newEmail;

        $this->recordDomainEvent(
            new UserEmailChanged($this->id(), $newEmail, $oldEmail)
        );
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): EmailAddress
    {
        return $this->email;
    }

    public function status(): UserStatus
    {
        return $this->status;
    }

    public function registeredAt(): \DateTimeImmutable
    {
        return $this->registeredAt;
    }
}
