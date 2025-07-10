<?php

declare(strict_types=1);

namespace Identity\Domain;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\Event\IdentityAttachedToAccount;
use Identity\Domain\Event\IdentityCreated;
use Identity\Domain\Exception\IdentityAlreadyExistsException;
use Identity\Domain\ValueObject\Identifier;
use Kernel\Domain\Aggregate\AggregateRoot;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Identity\Domain\AttachedIdentifier;
use Identity\Domain\Event\IdentityDetachedFromAccount;
use Identity\Domain\Exception\IdentityException;

final class UserIdentity extends AggregateRoot
{
    /** @var Collection<int, AttachedIdentifier> */
    private Collection $identifiers;

    /**
     * @param UserId $userId L'ID du compte auquel cette identité est liée.
     * @param AttachedIdentifier $primaryIdentifier Le premier identifiant (forcément primaire).
     */
    private function __construct(
        private(set) UserId $userId,
        private(set) AttachedIdentifier $primaryIdentifier,
    ) {
        $this->identifiers = new ArrayCollection([$primaryIdentifier]);
    }

    /**
     * Point d'entrée pour la création d'une nouvelle UserIdentity.
     */
    public static function create(UserId $userId, Identifier $identifier): self
    {
        $attachedIdentifier = AttachedIdentifier::fromIdentifier($identifier, true); // Le premier est primaire
        $self = new self($userId, $attachedIdentifier);

        $self->raise(
            new IdentityCreated($self->userId, $identifier)
        );

        return $self;
    }

    /**
     * Attache une nouvelle identité à ce compte.
     *
     * @throws IdentityAlreadyExistsException
     */
    public function attachIdentity(Identifier $newIdentifier): void
    {
        if ($this->hasIdentifier($newIdentifier)) {
            // Pas d'erreur si l'identité exacte est déjà là, c'est idempotent.
            return;
        }

        $isPrimary = $this->identifiers->isEmpty(); // Le premier est toujours primaire.
        $attachedIdentifier = AttachedIdentifier::fromIdentifier($newIdentifier, $isPrimary);
        $this->identifiers->add($attachedIdentifier);

        $this->raise(
            new IdentityAttachedToAccount($this->userId, $newIdentifier)
        );
    }

    /**
     * Détache un identifiant de ce compte.
     *
     * @throws IdentityException si l'identité n'existe pas ou est la seule identité primaire
     */
    public function detachIdentity(Identifier $identifierToRemove): void
    {
        $attachedIdentifierToRemove = null;

        /** @var AttachedIdentifier $attachedIdentifier */
        foreach ($this->identifiers as $key => $attachedIdentifier) {
            if ($attachedIdentifier->identifier->equals($identifierToRemove)) {
                $attachedIdentifierToRemove = $attachedIdentifier;
                break;
            }
        }

        if (null === $attachedIdentifierToRemove) {
            throw IdentityException::identityNotFound($identifierToRemove);
        }

        // Règle métier : ne pas supprimer la dernière identité
        if ($this->identifiers->count() === 1) {
            throw IdentityException::cannotRemoveLastIdentity();
        }

        // Règle métier : si on supprime l'identité primaire, promouvoir une autre
        if ($attachedIdentifierToRemove->isPrimary && $this->identifiers->count() > 1) {
            $this->promoteNextIdentityToPrimary($attachedIdentifierToRemove);
        }

        $this->identifiers->removeElement($attachedIdentifierToRemove);

        $this->raise(
            new IdentityDetachedFromAccount($this->userId, $identifierToRemove)
        );
    }

    /**
     * Vérifie si cet agrégat n'a plus d'identités.
     */
    public function hasNoIdentities(): bool
    {
        return $this->identifiers->isEmpty();
    }

    /**
     * Vérifie si un identifiant est l'identité primaire.
     */
    public function isPrimaryIdentity(Identifier $identifier): bool
    {
        /** @var AttachedIdentifier $attachedIdentifier */
        foreach ($this->identifiers as $attachedIdentifier) {
            if ($attachedIdentifier->identifier->equals($identifier)) {
                return $attachedIdentifier->isPrimary;
            }
        }

        return false;
    }

    /**
     * Corrige la méthode hasIdentifier pour qu'elle fonctionne vraiment.
     */
    private function hasIdentifier(Identifier $identifierToFind): bool
    {
        /** @var AttachedIdentifier $attached */
        foreach ($this->identifiers as $attached) {
            if ($attached->identifier->equals($identifierToFind)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Promeut la prochaine identité comme primaire quand on supprime l'actuelle primaire.
     */
    private function promoteNextIdentityToPrimary(AttachedIdentifier $currentPrimary): void
    {
        /** @var AttachedIdentifier $attachedIdentifier */
        foreach ($this->identifiers as $attachedIdentifier) {
            if ($attachedIdentifier !== $currentPrimary) {
                $attachedIdentifier->markAsPrimary();
                break;
            }
        }
    }
}
