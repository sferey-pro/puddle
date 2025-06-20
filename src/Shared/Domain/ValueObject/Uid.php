<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use Symfony\Component\Uid\AbstractUid;
use Symfony\Component\Uid\Uuid;

/**
 * Classe de base abstraite pour les identifiants uniques (UID).
 *
 * Cette classe encapsule un objet Uid de Symfony, fournissant une base solide
 * pour créer des identifiants typés (comme EventId, UserId, etc.) tout en centralisant
 * la logique de génération et de comparaison.
 */
abstract class Uid implements \Stringable
{
    final private function __construct(
        public AbstractUid $value,
    ) {
    }

    /**
     * Crée une nouvelle instance avec un Uid généré aléatoirement (UUIDv7).
     */
    public static function generate(): static
    {
        return new static(Uuid::v7());
    }

    /**
     * Crée une instance à partir d'une représentation textuelle d'un UUID.
     */
    public static function fromString(string $uid): static
    {
        return new static(Uuid::fromString($uid));
    }

    /**
     * Retourne la représentation textuelle de l'Uid.
     */
    public function __toString(): string
    {
        return (string) $this->value;
    }

    /**
     * Compare cet Uid à un autre pour vérifier l'égalité.
     */
    public function equals(?object $other): bool
    {
        return $other instanceof self && $this->value->equals($other->value);
    }
}
