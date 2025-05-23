<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Spotify\DTO\ResponseParts;

/**
 * Value Object représentant la structure "item" (la musique)
 * de la réponse de l'API Spotify "currently-playing".
 */
final readonly class ItemDTO
{
    public TrackDetailsDTO $trackDetails;
    public ArtistDTO $artist;
    public AlbumDTO $album;

    public function __construct(private array $itemData)
    {
        $this->trackDetails = new TrackDetailsDTO(
            $this->itemData['name'] ?? null,
            $this->itemData['external_urls']['spotify'] ?? null
        );
        $this->artist = new ArtistDTO($this->itemData['artists'] ?? null);
        $this->album = new AlbumDTO($this->itemData['album'] ?? null);
    }
}
