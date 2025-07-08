<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Core\Domain\Aggregate\AggregateRoot;
use App\Core\Domain\Event\DomainEventTrait;
use App\Module\Auth\Domain\Event\PasswordChanged;
use App\Module\Auth\Domain\Exception\PasswordResetException;
use App\Module\Auth\Domain\ValueObject\HashedPassword;
use App\Module\SharedContext\Domain\ValueObject\UserId;

/**
 * Agrégat qui représente le credential "mot de passe" pour un UserAccount.
 */
final class PasswordCredential extends AggregateRoot
{
    use DomainEventTrait;

    private function __construct(
        private(set) UserId $id,
        private(set) HashedPassword $password
    ) {
    }

    public static function create(UserId $userId, HashedPassword $password): self
    {
        return new self($userId, $password);
    }

    /**
     * Met à jour le mot de passe du compte.
     */
    public function changePassword(HashedPassword $password): void
    {
        $this->password = $password;

        $this->recordDomainEvent(
            new PasswordChanged($this->id)
        );
    }

    /**
     * Réinitialise le mot de passe de l'utilisateur après vérification du token.
     *
     * @throws PasswordResetException si la demande est invalide, expirée ou déjà utilisée
     */
    public function resetPassword(
        PasswordResetRequest $request,
        Password $newPassword,
        \DateTimeImmutable $now,
    ): void {
        if (!$this->id->equals($request->userId())) {
            throw PasswordResetException::userMismatch($request->userId(), $this->id());
        }

        if ($request->isExpired($now)) {
            throw PasswordResetException::expired();
        }

        if ($request->isUsed()) {
            throw PasswordResetException::alreadyUsed();
        }

        $this->changePassword($newPassword);

        $request->markAsUsed();
    }
}
