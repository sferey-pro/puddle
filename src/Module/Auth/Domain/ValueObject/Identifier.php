<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Module\Auth\Domain\Enum\IdentifierType;
use App\Module\Auth\Domain\Exception\InvalidIdentifierException;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Module\SharedContext\Domain\ValueObject\Phone;
use Webmozart\Assert\Assert;

final readonly class Identifier implements \Stringable
{
    public Email|Phone $value;
    public IdentifierType $type;

    private function __construct(
        Email|Phone $value,
        IdentifierType $type
    ) {
        Assert::notEmpty($value);

        if (null !== $value) {
            Assert::lengthBetween($value, 1, 180);
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        if (Email::validate($value)->isValid()) {
            return self::fromEmail($value);
        }

        if (Phone::validate($value)->isValid()) {
            return self::fromPhone($value);
        }

        throw InvalidIdentifierException::becauseItMustBeAnEmailOrPhone();
    }

    public static function fromEmail(string $email): self
    {
        return new self(new Email($email), IdentifierType::EMAIL);
    }

    public static function fromPhone(string $phoneNumber): self
    {
        return new self(new Phone($phoneNumber), IdentifierType::PHONE);
    }

    public function isEmail(): bool
    {
        return $this->type === IdentifierType::EMAIL;
    }

    public function isPhone(): bool
    {
        return $this->type === IdentifierType::PHONE;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function isEqualTo(self $identifier): bool
    {
        return $this->type === $identifier->type && $this->value->isEqualTo($identifier->value);
    }


}
