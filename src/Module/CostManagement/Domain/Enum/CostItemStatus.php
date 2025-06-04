<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Domain\Enum;

use App\Core\Enum\EnumJsonSerializableTrait;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum CostItemStatus: string implements TranslatableInterface
{
    use EnumJsonSerializableTrait;

    case ACTIVE = 'active';
    case FULLY_COVERED = 'fully_covered';
    case ARCHIVED = 'archived';

    public function equals(self $other): bool
    {
        return $this === $other;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::FULLY_COVERED => 'Entièrement Couvert',
            self::ARCHIVED => 'Archivé',
        };
    }

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return match ($this) {
            self::ACTIVE => $translator->trans('Active', locale: $locale),
            self::FULLY_COVERED => $translator->trans('Fully Covered', locale: $locale),
            self::ARCHIVED => $translator->trans('Archived', locale: $locale),
        };
    }
}
