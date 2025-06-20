<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Domain\Enum\UserStatus;
use App\Module\UserManagement\Domain\Event\UserAccountAnonymized;
use App\Module\UserManagement\Domain\Event\UserAccountDeactivated;
use App\Module\UserManagement\Domain\Event\UserAccountReactivated;
use App\Module\UserManagement\Domain\Event\UserCreated;
use App\Module\UserManagement\Domain\Event\UserDeleted;
use App\Module\UserManagement\Domain\Event\UserEmailChanged;
use App\Module\UserManagement\Domain\Event\UserProfileCompleted;
use App\Module\UserManagement\Domain\Exception\UserException;
use App\Module\UserManagement\Domain\Specification\UserCanBeDeactivatedSpecification;
use App\Module\UserManagement\Domain\Specification\UserHasStatusSpecification;
use App\Module\UserManagement\Domain\ValueObject\AvatarUrl;
use App\Module\UserManagement\Domain\ValueObject\Username;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEventTrait;
use App\Shared\Domain\Service\SystemTime;

/**
 * Agrégat représentant un utilisateur dans le contexte de UserManagement.
 *
 * Responsabilités:
 * - Encapsuler les informations de profil (nom, avatar...).
 * - Gérer le cycle de vie de l'utilisateur (statut actif, suspendu...).
 * - Garantir l'intégrité de ses propres données (invariants).
 */
final class User extends AggregateRoot
{
    use DomainEventTrait;

    private Email $email;
    private UserStatus $status;
    private readonly \DateTimeImmutable $registeredAt;

    private function __construct(
        private readonly UserId $id,
        Email $email,
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
        Email $email,
    ): self {
        $user = new self($id, $email);
        $user->status = UserStatus::PENDING;

        $user->recordDomainEvent(
            new UserCreated($user->id(), $user->email())
        );

        return $user;
    }

    /**
     * Cas d'usage pour compléter le profil pour la première fois.
     * C'est une transition d'état majeure pour l'utilisateur.
     */
    public function completeProfile(
        Username $username,
    ): void {
        $this->status = UserStatus::ACTIVE;

        $this->recordDomainEvent(new UserProfileCompleted($this->id));
    }

    public function delete(): self
    {
        $this->recordDomainEvent(
            new UserDeleted($this->id())
        );

        return $this;
    }

    public function changeEmail(Email $newEmail): void
    {
        if ($this->email->isEqualTo($newEmail)) {
            return;
        }

        $oldEmail = $this->email;
        $this->email = $newEmail;

        $this->recordDomainEvent(
            new UserEmailChanged($this->id(), $newEmail, $oldEmail)
        );
    }

    public function deactivate(?string $reason = null): void
    {
        $spec = new UserCanBeDeactivatedSpecification();

        if (!$spec->isSatisfiedBy($this)) {
            throw UserException::accountAlreadyDeactivated($this->id);
        }

        $this->status = UserStatus::DEACTIVATED;
        $this->recordDomainEvent(new UserAccountDeactivated($this->id, $reason));
    }

    public function reactivate(): void
    {
        $spec = new UserHasStatusSpecification(UserStatus::DEACTIVATED);

        if (!$spec->isSatisfiedBy($this)) {
            throw UserException::accountNotDeactivated($this->id);
        }

        $this->status = UserStatus::ACTIVE;
        $this->recordDomainEvent(new UserAccountReactivated($this->id));
    }

    public function anonymize(): void
    {
        $spec = new UserHasStatusSpecification(UserStatus::ANONYMIZED);
        if (!$spec->isSatisfiedBy($this)) {
            throw UserException::accountAlreadyAnonymized($this->id, $this->status);
        }

        // Anonymisation des données
        // $this->username = new Username('anonymized_'.$this->id);
        $this->email = new Email(\sprintf('%s@anonymous.puddle.com', $this->id));
        // $this->avatarUrl = null;

        $this->status = UserStatus::ANONYMIZED;
        $this->recordDomainEvent(new UserAccountAnonymized($this->id));
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function email(): Email
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
