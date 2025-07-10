<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Domain;

use App\Core\Application\Clock\SystemTime;
use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\Auth\Domain\ValueObject\EmailIdentity;
use App\Module\Auth\Domain\ValueObject\PhoneIdentity;
use App\Module\Auth\Domain\ValueObject\UserIdentity;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\PhoneNumber;
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

    private(set) UserId $id;
    private(set) UserStatus $status;
    private(set) ?EmailAddress $email = null;
    private(set) ?PhoneNumber $phone = null;

    private(set) \DateTimeImmutable $createdAt;
    private(set) \DateTimeImmutable $updatedAt;

    private function __construct() {}

    public static function create(UserId $id, UserIdentity $identity): self {
        $user = new self();
        $user->id = $id;
        $user->status = UserStatus::PENDING;

        match ($identity::class) {
            EmailIdentity::class => $user->email = $identity->email,
            PhoneIdentity::class => $user->phone = $identity->phone,
        };

        $user->recordDomainEvent(
            new UserCreated($user->id, $user->email, $user->phone)
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
            new UserEmailChanged($this->id, $newEmail, $oldEmail)
        );
    }
}
