<?php
namespace App\Module\Auth\Domain\AccessCredential;

final readonly class MagicLinkCredential implements AccessCredentialInterface
{
    public function __construct(
        public string $url
    ) {}

    public function getType(): string
    {
        return 'magic_link';
    }
}
