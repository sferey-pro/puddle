<?php

declare(strict_types=1);

namespace App\Module\Music\Domain\Port;

use App\Module\Music\Domain\Model\CurrentlyPlayingTrack;

interface CurrentlyPlayingMusicProviderInterface
{
    public function fetchCurrentlyPlaying(): ?CurrentlyPlayingTrack;
}
