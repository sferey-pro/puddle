<?php

declare(strict_types=1);

namespace App\Module\Music\Application\Command;

use App\Module\Music\Domain\Port\CurrentlyPlayingMusicProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Twig\Environment as TwigEnvironment;

#[AsMessageHandler()]
final class FetchCurrentlyPlayingMusicCommandHandler
{
    public function __construct(
        private readonly CurrentlyPlayingMusicProviderInterface $musicProvider,
        private readonly HubInterface $mercureHub,
        private readonly TwigEnvironment $twig,
        private readonly LoggerInterface $logger
    ) {
    }

    public function __invoke(FetchCurrentlyPlayingMusicCommand $command): void
    {
        $track = $this->musicProvider->fetchCurrentlyPlaying();

        $turboStream = $this->twig->render('@MusicUI/components/track.stream.html.twig', [
            'currentTrack' => $track,
        ]);

        $update = new Update(
            $command->mercureTopic,
            $turboStream,
        );

        $this->mercureHub->publish($update);
    }
}
