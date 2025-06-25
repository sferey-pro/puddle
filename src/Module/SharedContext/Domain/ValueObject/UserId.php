<?php

declare(strict_types=1);

namespace App\Module\SharedContext\Domain\ValueObject;

use App\Core\Domain\ValueObject\AggregateRootId;

/**
 * Représente l'identifiant unique d'un utilisateur (Aggregat User / Aggregat UserAccount).
 */
final class UserId extends AggregateRootId
{
}
