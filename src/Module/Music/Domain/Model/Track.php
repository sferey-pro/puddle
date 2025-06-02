<?php

declare(strict_types=1);

namespace App\Module\Music\Domain\Model;

final class Track
{
    public function __construct(
        public readonly ?string $trackName,
        public readonly ?string $trackUrl,
        public readonly ?string $artistName,
        public readonly ?string $albumName,
        public readonly ?string $albumArtUrl,
    ) {
    }

    public static function nothingPlaying(): self
    {
        return new self(null, null, null, null, null);
    }

    public static function create(
        ?string $trackName = null,
        ?string $trackUrl = null,
        ?string $artistName = null,
        ?string $albumName = null,
        ?string $albumArtUrl = null,
    ): self {
        return new self(
            trackName: $trackName,
            trackUrl: $trackUrl,
            artistName: $artistName,
            albumName: $albumName,
            albumArtUrl: $albumArtUrl
        );
    }
}
