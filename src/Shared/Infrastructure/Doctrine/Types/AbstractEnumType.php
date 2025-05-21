<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidFormat;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return $value;
        }

        if ($value instanceof \BackedEnum) {
            return $value->value;
        }

        throw InvalidType::new(
            $value,
            static::class,
            ['null', \BackedEnum::class],
        );
    }

    /**
     * @param ?string $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return $value;
        }

        if (true === enum_exists($this::getEnumsClass(), true)) {
            // 🔥 https://www.php.net/manual/en/backedenum.tryfrom.php
            return $this::getEnumsClass()::tryFrom($value);
        }

        throw InvalidFormat::new(
                $value,
                static::class,
                null
        );

    }

    /**
     * @return class-string<\BackedEnum>
     */
    abstract public static function getEnumsClass(): string;
}
