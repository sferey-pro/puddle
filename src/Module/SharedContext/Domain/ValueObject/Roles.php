<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Module\SharedContext\Domain\Enum\Role;
use Webmozart\Assert\Assert;

readonly class Roles implements \Stringable
{
    private array $roles;

    public function __construct(array $roles = [Role::USER])
    {
        Assert::notEmpty($roles, 'Roles cannot be empty');
        Assert::allIsInstanceOf($roles, Role::class);

        $roles[] = Role::USER;
        $this->roles = array_unique(\array_map(fn (Role $role) => $role->value, $roles));
    }

    public function __toString(): string
    {
        return implode(',', $this->roles);
    }

    public static function superAdmin(): self
    {
        return new self([Role::USER, Role::ADMIN, Role::SUPER_ADMIN]);
    }

    public static function admin(): self
    {
        return new self([Role::USER, Role::ADMIN]);
    }

    public static function user(): self
    {
        return new self();
    }

    public static function guest(): self
    {
        return new self([Role::GUEST]);
    }

    public function toArray(): array
    {
        return $this->roles;
    }

    public function toSecurityRoles(): array
    {
        return array_map(static fn (Role $role) => $role->toSecurityRole(), $this->roles);
    }

    public static function fromArray(array $roles): self
    {
        return new self($roles);
    }

    public function contains(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    public function equals(self $other): bool
    {
        $roles = sort($this->toArray());
        $otherRoles = sort($other->toArray());

        return $roles === $otherRoles;
    }
}
