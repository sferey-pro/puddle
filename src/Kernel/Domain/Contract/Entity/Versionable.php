<?php

namespace Kernel\Domain\Contract\Entity;

interface Versionable
{
    public function getVersion(): int;
    public function setVersion(int $version): void;
    public function incrementVersion(): void;
}
