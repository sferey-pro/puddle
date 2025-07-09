<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Types;

use App\Core\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;

/**
 * Classe de type Doctrine pour le ValueObject PasswordResetRequestId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class PasswordResetRequestIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const NAME = 'password_reset_request_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return PasswordResetRequestId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
