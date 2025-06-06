<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Spotify\DTO;

use App\Module\Music\Infrastructure\Spotify\DTO\ResponseParts\ItemDTO;

/**
 * Value Object représentant la réponse globale de l'API Spotify "currently-playing".
 */
final readonly class CurrentlyPlayingApiResponse
{
    private ?array $responseData;

    public function __construct(?array $responseData)
    {
        $this->responseData = $responseData;
    }

    public function isPlaying(): bool
    {
        return null !== $this->responseData
               && ($this->responseData['is_playing'] ?? false) === true
               && isset($this->responseData['item']);
    }

    public function getItem(): ?ItemDTO
    {
        return $this->isPlaying() && isset($this->responseData['item']) ? new ItemDTO($this->responseData['item']) : null;
    }
}
