<?php

declare(strict_types=1);

namespace App\Module\Music\Domain\Port;

interface AccessTokenProviderInterface
{
    /**
     * @return string|null le token d'accès utilisateur pour l'API Spotify
     */
    public function getAccessToken(): ?string;

    public function clearAccessToken(): void;
}
