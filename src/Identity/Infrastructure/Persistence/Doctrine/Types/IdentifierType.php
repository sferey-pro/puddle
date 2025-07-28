<?php

declare(strict_types=1);

namespace Identity\Infrastructure\Persistence\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Identity\Domain\ValueObject\PhoneIdentity;
use Kernel\Infrastructure\Persistence\Doctrine\Types\DoctrineCustomTypeInterface;

/**
 * Type Doctrine custom pour persister les Value Objects Identifier polymorphiques.
 */
final class IdentifierType extends Type implements DoctrineCustomTypeInterface
{
    public const string NAME = 'identifier_type';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getJsonTypeDeclarationSQL($column);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?Identifier
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $data = json_decode($value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON for Identifier: ' . $value);
        }

        $type = $data['type'] ?? null;
        $identifierValue = $data['value'] ?? null;

        return match ($type) {
            'email' => new EmailIdentity($identifierValue),
            'phone' => new PhoneIdentity($identifierValue),
            default => throw new \InvalidArgumentException("Unknown identifier type: {$type}")
        };
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Identifier) {
            throw new \InvalidArgumentException('Expected Identifier instance');
        }

        $type = match (true) {
            $value instanceof EmailIdentity => 'email',
            $value instanceof PhoneIdentity => 'phone',
            default => throw new \InvalidArgumentException('Unknown Identifier subtype')
        };

        return json_encode([
            'type' => $type,
            'value' => $value->value()
        ]);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
