<?php

declare(strict_types=1);

namespace App\Module\Music\Domain\Model;

final class CurrentlyPlayingTrack
{
    public function __construct(
        public readonly bool $isPlaying,
        public readonly ?string $trackName,
        public readonly ?string $trackUrl,
        public readonly ?string $artistName,
        public readonly ?string $albumName,
        public readonly ?string $albumArtUrl,
    ) {
    }

    public static function nothingPlaying(): self
    {
        return new self(false, null, null, null, null, null);
    }

    public static function create(
        bool $isPlayingApi,
        ?string $trackName = null,
        ?string $trackUrl = null,
        ?string $artistName = null,
        ?string $albumName = null,
        ?string $albumArtUrl = null,
    ): self {
        // La piste est considérée comme "en lecture" si l'API le dit ET qu'on a un nom de piste
        $actuallyPlaying = $isPlayingApi && null !== $trackName;

        return new self(
            isPlaying: $actuallyPlaying,
            trackName: $trackName,
            trackUrl: $trackUrl,
            artistName: $artistName,
            albumName: $albumName,
            albumArtUrl: $albumArtUrl
        );
    }
}
