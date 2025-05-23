<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Spotify\DTO\ValueObject;

/**
 * Value Object représentant la structure "item" (la musique)
 * de la réponse de l'API Spotify "currently-playing".
 */
final readonly class Item
{
    public TrackDetails $trackDetails;
    public Artist $artist;
    public Album $album;

    public function __construct(private array $itemData)
    {
        $this->trackDetails = new TrackDetails(
            $this->itemData['name'] ?? null,
            $this->itemData['external_urls']['spotify'] ?? null
        );
        $this->artist = new Artist($this->itemData['artists'] ?? null);
        $this->album = new Album($this->itemData['album'] ?? null);
    }
}
