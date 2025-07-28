<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObject;

/**
 * Contrat de base pour tous les objets de valeur (Value Objects) du domaine.
 *
 * Un Value Object est un objet immuable qui représente une caractéristique descriptive
 * du domaine, et qui est défini par la valeur de ses attributs. Deux Value Objects
 * sont considérés comme égaux si leurs attributs sont égaux.
 *
 * Cette interface sert de marqueur pour identifier clairement les Value Objects
 * dans le système et peut être utilisée pour des contraintes de type ou des
 * vérifications d'instance.
 */
interface ValueObjectInterface
{
    /**
     * Compare deux Value Objects par leur valeur.
     * Deux VOs sont égaux si toutes leurs propriétés sont égales.
     */
    public function equals(self $other): bool;

    /**
     * Retourne une représentation string du Value Object.
     * Utile pour le debugging et les logs.
     */
    public function __toString(): string;

    /**
     * Sérialise le Value Object en array.
     * Permet la reconstruction et la persistance.
     *
     * @return array<string, mixed>
     */
    public function __toArray(): array;

    /**
     * Crée une instance depuis un array.
     *
     * @param array<string, mixed> $data
     * @return static
     */
    public static function fromArray(array $data): mixed;
}
