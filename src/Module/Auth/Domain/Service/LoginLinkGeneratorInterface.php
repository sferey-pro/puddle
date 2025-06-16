<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Service;

use App\Module\Auth\Domain\UserAccount;
use App\Module\Auth\Domain\ValueObject\LoginLinkDetails;

/**
 * Port pour la génération des détails d'un lien de connexion.
 */
interface LoginLinkGeneratorInterface
{
    public function generate(UserAccount $user): LoginLinkDetails;
}
