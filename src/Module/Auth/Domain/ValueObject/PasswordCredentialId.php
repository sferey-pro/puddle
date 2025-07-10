<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\ValueObject\Uid;

/**
 * Représente l'identifiant unique du login avec password (PasswordCredential).
 */
final class PasswordCredentialId extends Uid
{
}
