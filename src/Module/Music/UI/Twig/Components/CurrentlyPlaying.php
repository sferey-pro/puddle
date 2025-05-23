<?php

declare(strict_types=1);

namespace App\Module\Music\UI\Twig\Components;

use App\Module\Music\Application\Query\GetCurrentlyPlayingMusicQuery;
use App\Module\Music\Domain\Model\CurrentlyPlayingTrack;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent()]
final class CurrentlyPlaying
{
    use DefaultActionTrait;

    public function __construct(
        private QueryBusInterface $queryBus,
    ) {
    }

    #[ExposeInTemplate]
    public function getTrack(): ?CurrentlyPlayingTrack
    {
        return $this->queryBus->ask(new GetCurrentlyPlayingMusicQuery());
    }
}
