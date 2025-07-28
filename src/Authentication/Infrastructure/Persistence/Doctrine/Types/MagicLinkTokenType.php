<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Types;

use Authentication\Domain\ValueObject\Token\MagicLinkToken;
use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectStringType;

final class MagicLinkTokenType extends AbstractValueObjectStringType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'magic_link_token';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return MagicLinkToken::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
