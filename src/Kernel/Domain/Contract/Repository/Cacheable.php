<?php

namespace Kernel\Domain\Contract\Repository;

interface Cacheable
{
    public function getCacheKey(mixed $id): string;
    public function getCacheTtl(): int;
    public function invalidateCache(mixed $id): void;
}