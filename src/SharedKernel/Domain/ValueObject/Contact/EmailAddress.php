<?php

declare(strict_types=1);

namespace SharedKernel\Domain\ValueObject\Contact;

use Assert\Assert;
use Kernel\Domain\ValueObject\AbstractStringValueObject;
use Kernel\Domain\ValueObject\UniqueValueInterface;
use Kernel\Domain\ValueObject\ValidatedValueObjectTrait;

final readonly class EmailAddress extends AbstractStringValueObject implements UniqueValueInterface
{
    use ValidatedValueObjectTrait;

    private const int MAX_LENGTH = 180;

    protected static function validate(...$args): void
    {
        if (count($args) !== 1 || !is_string($args[0])) {
            throw new \InvalidArgumentException('EmailAddress::validate() expects exactly one string argument');
        }

        $email = $args[0];
        $normalizedEmail = mb_strtolower(trim($email));

        Assert::that($normalizedEmail)
            ->notEmpty('Email address cannot be empty')
            ->maxLength(self::MAX_LENGTH, sprintf(
                'Email address cannot exceed %d characters',
                self::MAX_LENGTH
            ))
            ->email('"%s" is not a valid email address');

        // Validation supplémentaire du domaine
        $domain = substr($normalizedEmail, strrpos($normalizedEmail, '@') + 1);

        Assert::that($domain)
            ->notEmpty('Email domain cannot be empty')
            ->regex(
                '/^[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,}$/i',
                'Invalid email domain format'
            );
    }

    public static function uniqueFieldPath(): string
    {
        return 'email';
    }

    public function uniqueValue(): string
    {
        return $this->value;
    }

    /**
     * Helpers métier.
     */
    public function getDomain(): string
    {
        return substr($this->value, strrpos($this->value, '@') + 1);
    }

    public function getLocalPart(): string
    {
        return substr($this->value, 0, strrpos($this->value, '@'));
    }
}
