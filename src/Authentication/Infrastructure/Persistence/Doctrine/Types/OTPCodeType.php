<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Types;

use Authentication\Domain\ValueObject\Token\OtpCode;
use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;

final class OTPCodeType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'otp_code';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return OtpCode::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
