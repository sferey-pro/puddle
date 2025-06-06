<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Spotify\DTO\ResponseParts;

final readonly class ArtistDTO
{
    public ?string $name;

    public function __construct(?array $artistsData)
    {
        if (empty($artistsData)) {
            $this->name = null;

            return;
        }
        $this->name = implode(', ', array_column($artistsData, 'name'));
    }
}
