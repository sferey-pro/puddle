<?php

declare(strict_types=1);

namespace App\Module\Music\Domain\Exception;

class AccessTokenUnavailableException extends \LogicException
{
    public function __construct()
    {
        parent::__construct('Spotify access token is not configured.');
    }
}
