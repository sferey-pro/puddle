<?php

declare(strict_types=1);

namespace App\Module\Music\Infrastructure\Provider;

use App\Module\Music\Domain\Exception\AccessTokenUnavailableException;
use App\Module\Music\Domain\Model\CurrentlyPlayingTrack;
use App\Module\Music\Domain\Port\AccessTokenProviderInterface;
use App\Module\Music\Domain\Port\CurrentlyPlayingMusicProviderInterface;
use App\Module\Music\Infrastructure\Spotify\DTO\CurrentlyPlayingApiResponse;
use App\Module\Music\Infrastructure\Spotify\DTO\ValueObject\Item;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpotifyCurrentlyPlayingMusicProvider implements CurrentlyPlayingMusicProviderInterface
{
    public function __construct(
        private readonly AccessTokenProviderInterface $authTokenProvider,
        private HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'SPOTIFY_CURRENTLY_PLAYING_URL')]
        private string $apiCurrentlyPlayingUrl,
    ) {
    }

    public function fetchCurrentlyPlaying(): CurrentlyPlayingTrack
    {
        try {
            $accessToken = $this->authTokenProvider->getAccessToken();
            $response = $this->requestCurrentlyPlaying($accessToken);

            if (Response::HTTP_NO_CONTENT === $response->getStatusCode()) {
                // Rien n'est en cours de lecture
                return CurrentlyPlayingTrack::nothingPlaying();
            }

            if (Response::HTTP_OK !== $response->getStatusCode()) {
                $this->logger->warning('Failed to get currently playing track from Spotify', [
                    'status_code' => $response->getStatusCode(),
                    'content' => $response->getContent(false),
                ]);
                if (Response::HTTP_UNAUTHORIZED === $response->getStatusCode()) {
                    $this->authTokenProvider->clearAccessToken();
                } // Force refresh next time

                return CurrentlyPlayingTrack::nothingPlaying();
            }

            $response = new CurrentlyPlayingApiResponse($response->toArray());

            /** @var Item|null $item */
            if (!$item = $response->getItem()) {
                return CurrentlyPlayingTrack::create($response->isPlaying());
            }

            return CurrentlyPlayingTrack::create(
                isPlayingApi: $response->isPlaying(),
                trackName: $item->trackDetails->name,
                trackUrl: $item->trackDetails->url,
                artistName: $item->artist->name,
                albumName: $item->album->name,
                albumArtUrl: $item->album->artUrl,
            );
        } catch (AccessTokenUnavailableException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e]);

            return CurrentlyPlayingTrack::nothingPlaying();
        } catch (ExceptionInterface $e) {
            $this->logger->error('Spotify HTTP client exception: '.$e->getMessage(), ['exception' => $e]);

            return CurrentlyPlayingTrack::nothingPlaying();
        }
    }

    private function requestCurrentlyPlaying(string $accessToken): ResponseInterface
    {
        return $this->httpClient->request('GET', $this->apiCurrentlyPlayingUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$accessToken,
                'Accept' => 'application/json',
            ],
        ]);
    }
}
