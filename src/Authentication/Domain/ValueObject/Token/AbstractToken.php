<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject\Token;

use Authentication\Domain\ValueObject\Token as TokenInterface;
use Kernel\Domain\ValueObject\ValueObjectInterface;

abstract class AbstractToken implements TokenInterface, ValueObjectInterface
{
    protected function __construct(
        protected readonly string $value,
        protected readonly \DateTimeImmutable $expiresAt
    ) {
        $this->validate();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function matches(string $value): bool
    {
        return hash_equals($this->value, $value);
    }

    public function isExpired(\DateTimeImmutable $now): bool
    {
        return $now > $this->expiresAt;
    }

    public function expiresAt(): \DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function equals(ValueObjectInterface $other): bool
    {
        return $other instanceof self
            && $this->matches($other->value())
            && $this->expiresAt->getTimestamp() === $other->expiresAt()->getTimestamp();
    }

    public function __toString(): string
    {
        // Masque le token pour la sécurité
        $masked = substr($this->value, 0, 6) . '...' . substr($this->value, -4);
        return sprintf('%s[%s, expires: %s]',
            $this->type(),
            $masked,
            $this->expiresAt->format('Y-m-d H:i:s')
        );
    }

    public function __toArray(): array
    {
        return [
            'type' => $this->type(),
            'value' => $this->value,
            'expiresAt' => $this->expiresAt->format('c')
        ];
    }

    public static function fromArray(array $data): static
    {
        if (!isset($data['value'], $data['expiresAt'])) {
            throw new \InvalidArgumentException('Missing required fields: value, expiresAt');
        }

        return new static(
            $data['value'],
            new \DateTimeImmutable($data['expiresAt'])
        );
    }

    abstract protected function validate(): void;
}
