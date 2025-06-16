<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

final readonly class LoginLinkDetails
{
    public function __construct(
        public Hash $hash,
        public \DateTimeImmutable $expiresAt,
        public string $url, // L'URL complète pour l'envoyer par email
    ) {
    }

    public function url(): string
    {
        return $this->url;
    }
}
