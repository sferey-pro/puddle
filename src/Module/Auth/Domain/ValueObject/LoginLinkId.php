<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\ValueObject\Uid;

/**
 * Représente l'identifiant unique du login user par lien magique (LoginLink).
 */
final class LoginLinkId extends Uid
{
}
