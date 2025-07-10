<?php
namespace App\Module\Auth\Domain\AccessCredential;

final readonly class MagicLinkCredential implements AccessCredential
{
    public function __construct(
        public string $url
    ) {}

    public function getType(): string
    {
        return 'magic_link';
    }
}