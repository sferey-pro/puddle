<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\Enum;

use App\Core\Domain\Enum\EnumJsonSerializableTrait;

/**
 * @see Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter
 *
 * Roles interne, défini par symfony.
 * @see AuthenticatedVoter::IS_AUTHENTICATED_FULLY
 * @see AuthenticatedVoter::IS_AUTHENTICATED_REMEMBERED
 * @see AuthenticatedVoter::IS_AUTHENTICATED
 * @see AuthenticatedVoter::IS_IMPERSONATOR
 * @see AuthenticatedVoter::IS_REMEMBERED
 * @see AuthenticatedVoter::PUBLIC_ACCESS
 */
enum Role: string
{
    use EnumJsonSerializableTrait;

    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case USER = 'user';
    case GUEST = 'guest';
    case ALLOWED_TO_SWITCH = 'allowed_to_switch';

    public function getLabel(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN => 'Admin',
            self::USER => 'User',
            self::GUEST => 'Guest',
            self::ALLOWED_TO_SWITCH => 'Allowed To Switch',
        };
    }

    /**
     * @return array{label: string, color: string}
     */
    public function getBadgeConfiguration(): array
    {
        return [
            'label' => $this->getLabel(),
            'color' => match ($this) {
                self::SUPER_ADMIN => 'red',
                self::ADMIN => 'orange',
                self::USER => 'green',
                self::GUEST => 'grey',
                self::ALLOWED_TO_SWITCH => 'blue',
            },
        ];
    }

    public function toSecurityRole(): string
    {
        return 'ROLE_' . mb_strtoupper($this->value);
    }

    public function equals(self $other): bool
    {
        return $this === $other;
    }
}
