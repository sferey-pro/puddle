<?php

namespace Kernel\Domain\Contract\Entity;

interface Blameable
{
    public function getCreatedBy(): ?UserId;
    public function getUpdatedBy(): ?UserId;
    public function setCreatedBy(UserId $userId): void;
    public function setUpdatedBy(UserId $userId): void;
}