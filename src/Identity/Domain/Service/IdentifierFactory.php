<?php

declare(strict_types=1);

namespace Identity\Domain\Service;

use Identity\Domain\Exception\InvalidIdentifierException;
use Identity\Domain\ValueObject\EmailIdentity;
use Identity\Domain\ValueObject\Identifier;
use Identity\Domain\ValueObject\PhoneIdentity;

final class IdentifierFactory
{

    /**
     * Map des types supportés vers leurs classes
     */
    private const TYPE_CLASS_MAP = [
        'email' => EmailIdentity::class,
        'phone' => PhoneIdentity::class,
        // 'loyalty_card' => LoyaltyCardIdentity::class,
    ];

    /**
     * Crée un Identifier à partir du type et de la valeur
     *
     * @throws \InvalidArgumentException Si le type n'est pas supporté
     * @throws \RuntimeException Si la création échoue
     */
    public static function create(string $type, string $value): Identifier
    {
        if (!isset(self::TYPE_CLASS_MAP[$type])) {
            throw new InvalidIdentifierException(
                sprintf('Unsupported identifier type "%s". Supported types: %s',
                    $type,
                    implode(', ', array_keys(self::TYPE_CLASS_MAP))
                )
            );
        }

        $class = self::TYPE_CLASS_MAP[$type];

        $result = $class::create($value);

        if ($result->isFailure()) {
            throw new \RuntimeException(sprintf(
                'Failed to create %s identifier: %s',
                $type,
                $result->error
            ));
        }

        return $result->value();
    }

    /**
     * Tente de créer un Identifier, retourne null en cas d'échec
     */
    public static function tryCreate(string $type, string $value): ?Identifier
    {
        try {
            return self::create($type, $value);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * Détecte automatiquement le type d'identifier
     */
    public static function createFromValue(string $value): Identifier
    {
        // Email pattern
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return self::create('email', $value);
        }

        // Phone pattern (très basique, à adapter selon vos besoins)
        if (preg_match('/^\+?[0-9]{10,15}$/', $value)) {
            return self::create('phone', $value);
        }

        throw new \InvalidArgumentException(sprintf(
            'Cannot determine identifier type for value: %s',
            $value
        ));
    }

    /**
     * Retourne tous les types supportés.
     *
     * @return string[]
     */
    public function getSupportedTypes(): array
    {
        return array_keys(self::TYPE_CLASS_MAP);
    }

    /**
     * Vérifie si un type est supporté.
     */
    public function isTypeSupported(string $type): bool
    {
        return isset(self::TYPE_CLASS_MAP[$type]);
    }
}
