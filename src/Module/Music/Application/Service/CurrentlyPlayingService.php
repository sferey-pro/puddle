<?php

declare(strict_types=1);

namespace App\Module\Music\Application\Service;

use App\Module\Music\Application\DTO\CurrentlyPlayingTrackInfo;
use App\Module\Music\Infrastructure\SpotifyApiAdapter;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;

class CurrentlyPlayingService
{
    private const CURRENTLY_PLAYING_CACHE_KEY = 'spotify_currently_playing';
    private const CURRENTLY_PLAYING_CACHE_TTL = 15; // Cache for 15 seconds

    public function __construct(
        private SpotifyApiAdapter $spotifyApiAdapter,
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger)
    {
    }

    public function getCurrentlyPlayingTrack(): CurrentlyPlayingTrackInfo
    {
        return $this->cache->get(self::CURRENTLY_PLAYING_CACHE_KEY, function ($item) {
            $item->expiresAfter(self::CURRENTLY_PLAYING_CACHE_TTL);

            $this->logger->info('Fetching currently playing track from Spotify API.');

            return $this->spotifyApiAdapter->getCurrentlyPlaying();
        });
    }
}
