<?php

declare(strict_types=1);

namespace Authentication\Domain\Repository;

use Authentication\Domain\Model\LoginAttempt;

interface LoginAttemptRepositoryInterface
{
    // ========== CRUD ==========
    public function save(LoginAttempt $attempt): void;
}
