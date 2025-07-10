<?php

namespace Kernel\Domain\Contract\Entity;

interface SoftDeletable
{
    public function delete(): void;
    public function restore(): void;
    public function isDeleted(): bool;
    public function getDeletedAt(): ?\DateTimeImmutable;
}