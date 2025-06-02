<?php

declare(strict_types=1);

namespace App\Module\Music\UI\Twig\Components;

use App\Module\Music\Application\Command\FetchCurrentlyPlayingMusicCommand;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent(template: '@MusicUI/components/CurrentlyPlaying.html.twig')]
final class CurrentlyPlaying
{
    use DefaultActionTrait;

    #[ExposeInTemplate]
    public string $mercureTopic = 'currently-playing-music';


    public function __construct(
        private MessageBusInterface $bus,
    ) {
    }

    public function mount(): void
    {
        $this->bus->dispatch(new FetchCurrentlyPlayingMusicCommand($this->mercureTopic));
    }

    #[LiveAction]
    public function requestUpdate(): void
    {
        $this->bus->dispatch(new FetchCurrentlyPlayingMusicCommand($this->mercureTopic));
    }
}
