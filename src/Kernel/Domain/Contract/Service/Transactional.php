<?php

namespace Kernel\Domain\Contract\Service;

interface Transactional
{
    /**
     * Indique que cette opération doit être exécutée dans une transaction.
     */
    public function isTransactional(): bool;
    
    /**
     * Niveau d'isolation requis (si applicable).
     */
    public function getIsolationLevel(): ?string;
}