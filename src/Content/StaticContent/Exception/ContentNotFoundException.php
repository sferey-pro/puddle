<?php

declare(strict_types=1);

namespace Content\StaticContent\Exception;

final class ContentNotFoundException extends \InvalidArgumentException
{
    private function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function forPage(string $pageKey): ContentNotFoundException
    {
        return new ContentNotFoundException('Page not found: ' . $pageKey);
    }
}
