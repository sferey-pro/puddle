<?php

namespace Kernel\Domain\Contract\Aggregate;

interface Snapshotable
{
    public function toSnapshot(): array;
    public static function fromSnapshot(array $data): static;
    public function getSnapshotVersion(): int;
}