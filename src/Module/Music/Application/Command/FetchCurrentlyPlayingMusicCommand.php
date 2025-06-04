<?php

declare(strict_types=1);

namespace App\Module\Music\Application\Command;

final class FetchCurrentlyPlayingMusicCommand
{
    public function __construct(
        public readonly string $mercureTopic,
    ) {
    }
}
