<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Spotify\DTO\ResponseParts;

final readonly class TrackDetailsDTO
{
    public ?string $name;
    public ?string $url;

    public function __construct(?string $name, ?string $url)
    {
        $this->name = $name;
        $this->url = $url;
    }
}
