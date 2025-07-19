<?php

declare(strict_types=1);

namespace Authentication\Infrastructure\Service;

use Authentication\Domain\Service\OTPServiceInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

final class OTPService implements OTPServiceInterface
{
    public function __construct(
        private readonly AdapterInterface $cache
    ) {}

    public function generateOTP(string $identifier): string
    {
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $this->storeOTP($identifier, $code);
        return $code;
    }

    public function storeOTP(string $identifier, string $code, int $ttl = 900): void
    {
        $item = $this->cache->getItem('otp_' . sha1($identifier));
        $item->set($code);
        $item->expiresAfter($ttl);
        $this->cache->save($item);
    }

    public function verifyOTP(string $identifier, string $code): bool
    {
        $item = $this->cache->getItem('otp_' . sha1($identifier));

        if (!$item->isHit()) {
            return false;
        }

        $valid = $item->get() === $code;

        if ($valid) {
            $this->cache->deleteItem('otp_' . sha1($identifier));
        }

        return $valid;
    }
}
