<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain;

use App\Core\Domain\Aggregate\AggregateRoot;
use App\Module\Auth\Domain\ValueObject\UserActivityId;
use App\Module\SharedContext\Domain\ValueObject\UserId;

final class UserActivity extends AggregateRoot
{
    private(set) UserActivityId $id;
    private(set) UserId $userId;

    private(set) ?\DateTimeImmutable $firstLoginAt = null;
    private(set) ?\DateTimeImmutable $lastLoginAt = null;

    private(set) int $loginCount = 0;

    private function __construct() {}

    public static function create(UserActivityId $id, UserId $userId): self
    {
        $activity = new self();
        $activity->id = $id;
        $activity->userId = $userId;

        return $activity;
    }

    public function recordLogin(): void
    {
        if (!$this->hasAlreadyLoggedIn()) {
            $this->firstLoginAt = new \DateTimeImmutable();
        }

        $this->lastLoginAt = new \DateTimeImmutable();
        $this->loginCount++;
    }

    /**
     * Vérifie si l'utilisateur s'est déjà connecté au moins une fois.
     */
    public function hasAlreadyLoggedIn(): bool
    {
        return null !== $this->firstLoginAt;
    }
}
