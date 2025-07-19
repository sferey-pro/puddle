<?php

declare(strict_types=1);

namespace Account\Lifecycle\Infrastructure\Persistence\Doctrine\Types;

use Account\Lifecycle\Domain\Model\AccountState;
use Account\Lifecycle\Domain\Model\State\ActiveState;
use Account\Lifecycle\Domain\Model\State\DeletedState;
use Account\Lifecycle\Domain\Model\State\InactiveState;
use Account\Lifecycle\Domain\Model\State\LockedState;
use Account\Lifecycle\Domain\Model\State\PendingState;
use Account\Lifecycle\Domain\Model\State\SuspendedState;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonType;

final class AccountStateType extends JsonType
{
    public const string NAME = 'account_state';

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!$value instanceof AccountState) {
            throw new \InvalidArgumentException('Value must be an AccountState');
        }

        $data = [
            'name' => $value->getName(),
            'class' => get_class($value),
            'data' => $this->extractStateData($value),
            'changed_at' => (new \DateTimeImmutable())->format('c'),
        ];

        return json_encode($data);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?AccountState
    {
        if ($value === null) {
            return null;
        }

        $data = json_decode($value, true);

        return match ($data['name']) {
            'pending' => new PendingState(),
            'active' => new ActiveState(),
            'suspended' => new SuspendedState(
                $data['data']['reason'],
                isset($data['data']['until'])
                    ? new \DateTimeImmutable($data['data']['until'])
                    : null
            ),
            'locked' => new LockedState($data['data']['reason']),
            'inactive' => new InactiveState(
                new \DateTimeImmutable($data['data']['since'])
            ),
            'deleted' => new DeletedState(),
            default => throw new \RuntimeException("Unknown state: {$data['name']}")
        };
    }

    private function extractStateData(AccountState $state): array
    {
        return match ($state::class) {
            SuspendedState::class => [
                'reason' => $state->reason,
                'until' => $state->until?->format('c'),
            ],
            LockedState::class => [
                'reason' => $state->reason,
            ],
            InactiveState::class => [
                'since' => $state->inactiveSince->format('c'),
            ],
            default => [],
        };
    }

    public function getName(): string
    {
        return self::NAME;
    }
}
