<?php

declare(strict_types=1);

namespace SharedKernel\Presentation\Twig\Components\UI;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'Alert',
    template: '@SharedKernel/components/UI/Alert.html.twig'
)]
class Alert
{
    public string $type = 'info';
    public string $message = '';
    public bool $dismissible = true;
    public string $format = 'standard';
    public ?string $icon = null;

    public function getIcon(): string
    {
        return match ($this->icon) {
            'success' => 'tabler:check',
            'error' => 'tabler:exclamation-circle',
            'warning' => 'tabler:alert-triangle',
            default => 'tabler:info-circle',
        };
    }
}
