<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Spotify\DTO\ValueObject;

final readonly class Album
{
    public ?string $name;
    public ?string $artUrl;

    public function __construct(?array $albumData)
    {
        $this->name = $albumData['name'] ?? null;
        $this->artUrl = $this->extractAlbumArtUrl($albumData['images'] ?? []);
    }

    private function extractAlbumArtUrl(array $images): ?string
    {
        if (empty($images)) {
            return null;
        }

        // Spotify fournit plusieurs tailles, on essaie de prendre une taille moyenne (index 1)
        // ou la première disponible.
        // Les tailles typiques sont [0] => 640x640, [1] => 300x300, [2] => 64x64
        if (isset($images[1]['url'])) {
            return $images[1]['url'];
        }

        return $images[0]['url'] ?? ($images[\count($images) - 1]['url'] ?? null);
    }
}
