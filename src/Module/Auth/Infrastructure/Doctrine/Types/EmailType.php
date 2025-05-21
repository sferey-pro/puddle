<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Types;

use App\Module\SharedContext\Domain\ValueObject\Email;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class EmailType extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'FLOAT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return new Email((string) $value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value->value;
    }

    public function getName()
    {
        return 'email';
    }
}
