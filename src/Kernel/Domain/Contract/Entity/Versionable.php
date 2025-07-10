<?php

namespace Kernel\Domain\Contract\Entity;

interface Versionable
{
    public function getVersion(): int;
    public function incrementVersion(): void;
}