<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Persistence\Doctrine\Types;

use Authentication\Domain\Model\Identity\LoginAttemptId;
use Kernel\Infrastructure\Persistence\Doctrine\Types\AbstractValueObjectIdType;

/**
 * Classe de type Doctrine pour le ValueObject LoginAttemptId.
 * Permet à Doctrine de comprendre comment stocker et récupérer cet objet.
 */
final class LoginAttemptIdType extends AbstractValueObjectIdType
{
    /**
     * Le nom unique de notre type pour Doctrine.
     */
    public const string NAME = 'login_attempt_id';

    /**
     * Spécifie la classe du ValueObject que ce type gère.
     */
    protected function getValueObjectClass(): string
    {
        return LoginAttemptId::class;
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
