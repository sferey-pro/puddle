<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class BootstrapModal
{
    public ?string $id = null;
}
