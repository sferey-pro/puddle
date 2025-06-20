<?php

declare(strict_types=1);

namespace App\Shared\UI\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class Alert
{
    public bool $description = false;
    public bool $dismissible = false;
    public string $style = 'basic';
    public string $type = 'success';
    public string $message;

    public function getIcon(): string
    {
        return match ($this->type) {
            'success' => 'tabler:check',
            'error' => 'tabler:exclamation-circle',
            'warning' => 'tabler:alert-triangle',
            'notice' => 'tabler:info-circle',
        };
    }
}
