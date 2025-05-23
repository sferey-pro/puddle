<?php

declare(strict_types=1);

namespace App\Module\Music\Application\Query;

use App\Module\Music\Domain\Model\CurrentlyPlayingTrack;
use App\Module\Music\Domain\Port\CurrentlyPlayingMusicProviderInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final class GetCurrentlyPlayingMusicQueryHandler
{
    public function __construct(
        private readonly CurrentlyPlayingMusicProviderInterface $musicProvider,
    ) {
    }

    public function __invoke(GetCurrentlyPlayingMusicQuery $query): CurrentlyPlayingTrack
    {
        return $this->musicProvider->fetchCurrentlyPlaying();
    }
}
