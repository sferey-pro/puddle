<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Provider;

use App\Module\Music\Domain\Port\AccessTokenProviderInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SpotifyAccessTokenProvider implements AccessTokenProviderInterface
{
    private const SPOTIFY_API_TOKEN_URL = 'https://accounts.spotify.com/api/token';
    private const ACCESS_TOKEN_CACHE_KEY = 'spotify_access_token';
    private const ACCESS_TOKEN_EXPIRY_SECONDS = 3500; // Tokens last 3600s, refresh a bit earlier

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire(env: 'SPOTIFY_CLIENT_ID')]
        private string $clientId,
        #[Autowire(env: 'SPOTIFY_CLIENT_SECRET')]
        private string $clientSecret,
        #[Autowire(env: 'SPOTIFY_REFRESH_TOKEN')]
        private string $refreshToken,
        private CacheItemPoolInterface $cache,
        private LoggerInterface $logger,
    ) {
    }

    public function getAccessToken(): ?string
    {
        $cachedTokenItem = $this->cache->getItem(self::ACCESS_TOKEN_CACHE_KEY);

        if ($cachedTokenItem->isHit()) {
            $this->logger->debug('Spotify app token found in cache.');

            return $cachedTokenItem->get();
        }

        try {
            $response = $this->requestAccessToken();

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                $this->logger->error('Failed to refresh Spotify access token', [
                    'status_code' => $response->getStatusCode(),
                    'content' => $response->getContent(false),
                ]);
                $this->cache->deleteItem(self::ACCESS_TOKEN_CACHE_KEY);

                return null;
            }

            return $cachedTokenItem
                ->set($response->toArray()['access_token'])
                ->expiresAfter(self::ACCESS_TOKEN_EXPIRY_SECONDS)
                ->get() ?? null;
        } catch (\Throwable $e) {
            $this->logger->error('Exception while refreshing Spotify access token', ['exception' => $e]);

            return null;
        }
    }

    public function clearAccessToken(): void
    {
        $this->cache->deleteItem(self::ACCESS_TOKEN_CACHE_KEY);
    }

    private function requestAccessToken(): ResponseInterface
    {
        return $this->httpClient->request(Request::METHOD_POST, self::SPOTIFY_API_TOKEN_URL, [
            'headers' => [
                'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->refreshToken,
            ],
        ]);
    }
}
