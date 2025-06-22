<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Infrastructure\Doctrine\Types;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

/**
 * Classe de type Doctrine pour le ValueObject UserId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class UserIdType extends Type
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const TYPE_NAME = 'user_id';

    public function getName(): string
    {
        return self::TYPE_NAME;
    }

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getUidClass(): string
    {
        return UserId::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        // Force le type de la colonne en base de données à être un UUID
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?UserId
    {
        if ($value instanceof UserId || null === $value) {
            return $value;
        }

        if (!\is_string($value)) {
            $this->throwInvalidType($value);
        }

        try {
            // On crée notre Value Object à partir de la chaîne de caractères.
            return $this->getUidClass()::fromString($value);
        } catch (\InvalidArgumentException $e) {
            $this->throwValueNotConvertible($value, $e);
        }
    }

    /**
     * @throws ConversionException
     */
    public function convertToDatabaseValue($userId, AbstractPlatform $platform): ?string
    {
        $toString = $this->hasNativeGuidType($platform) ? 'toRfc4122' : 'toBinary';

        if ($userId instanceof UserId) {
            return $userId->value->$toString();
        }

        if (null === $userId || '' === $userId) {
            return null;
        }

        if (!\is_string($userId)) {
            $this->throwInvalidType($userId);
        }

        try {
            return $this->getUidClass()::fromString($userId)->$toString();
        } catch (\InvalidArgumentException $e) {
            $this->throwValueNotConvertible($userId, $e);
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
        throw InvalidType::new($value, $this->getName(), ['null', 'string', $this->getUidClass()]);
    }

    private function throwValueNotConvertible(mixed $value, \Throwable $previous): never
    {
        throw ValueNotConvertible::new($value, $this->getName(), null, $previous);
    }
}
