<?php

declare(strict_types=1);

namespace SharedKernel\Presentation\Twig\Components\UI;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class BootstrapModal
{
    public ?string $id = null;
}
