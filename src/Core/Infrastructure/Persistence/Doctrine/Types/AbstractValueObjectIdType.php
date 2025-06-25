<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Persistence\Doctrine\Types;

use App\Core\Domain\ValueObject\AggregateRootId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

/**
 * Classe de base pour les types Doctrine qui gèrent nos Value Objects d'ID d'agrégat.
 *
 * Le problème que cette classe résout est que nos VOs (ex: UserId) ne sont pas
 * des Uuid, mais des objets qui encapsulent un Uuid dans une propriété `$value`.
 * Cette classe abstraite contient la logique générique pour convertir correctement
 * ces objets en leur représentation en base de données et vice-versa.
 */
abstract class AbstractValueObjectIdType extends Type
{
    /**
     * Doit retourner le nom unique du type Doctrine (ex: 'user_id', 'saga_state_id').
     * Ce nom sera utilisé dans les fichiers de mapping XML.
     */
    abstract public function getName(): string;

    /**
     * Doit retourner le FQCN (Fully Qualified Class Name) de la classe du Value Object.
     * Ex: App\Module\SharedContext\Domain\ValueObject\UserId::class.
     */
    abstract protected function getValueObjectClass(): string;

    /**
     * Définit le type SQL de la colonne en base de données.
     * Nos IDs étant basés sur des UUID, nous utilisons le type GUID de la plateforme.
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Force le type de la colonne en base de données à être un UUID
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    /**
     * Convertit la valeur de la base de données (string) en notre objet PHP (Value Object).
     *
     * @param ?string $value la valeur brute (UUID) venant de la BDD
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?AggregateRootId
    {
        $voClass = $this->getValueObjectClass();
        if ($value instanceof $voClass || null === $value) {
            return $value;
        }

        if (!\is_string($value)) {
            $this->throwInvalidType($value);
        }

        try {
            return $voClass::fromString($value);
        } catch (\InvalidArgumentException $e) {
            $this->throwValueNotConvertible($value, $e);
        }
    }

    /**
     * Convertit notre objet PHP (Value Object) en sa représentation en base de données (string).
     *
     * @param ?AggregateRootId $valueObject le Value Object à convertir
     *
     * @throws ConversionException
     */
    public function convertToDatabaseValue($valueObject, AbstractPlatform $platform): ?string
    {
        $toString = $this->hasNativeGuidType($platform) ? 'toRfc4122' : 'toBinary';

        $voClass = $this->getValueObjectClass();

        if ($valueObject instanceof $voClass) {
            return $valueObject->value->$toString();
        }

        if (null === $valueObject || '' === $valueObject) {
            return null;
        }

        if (!$valueObject instanceof AggregateRootId) {
            $this->throwInvalidType($valueObject);
        }

        try {
            return $this->getValueObjectClass()::fromString($valueObject)->$toString();
        } catch (\InvalidArgumentException $e) {
            $this->throwValueNotConvertible($valueObject, $e);
        }
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    private function hasNativeGuidType(AbstractPlatform $platform): bool
    {
        return $platform->getGuidTypeDeclarationSQL([]) !== $platform->getStringTypeDeclarationSQL(['fixed' => true, 'length' => 36]);
    }

    private function throwInvalidType(mixed $value): never
    {
        throw InvalidType::new($value, $this->getName(), ['null', 'string', $this->getValueObjectClass()]);
    }

    private function throwValueNotConvertible(mixed $value, \Throwable $previous): never
    {
        throw ValueNotConvertible::new($value, $this->getName(), null, $previous);
    }
}
