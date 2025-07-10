<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Symfony\Messenger\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class AsEventHandler
{
}
