<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\AccessCredential;

/**
 * Représente un moyen d'accès temporaire et à usage unique pour un utilisateur.
 * C'est le contrat pour des implémentations comme un lien magique ou un OTP.
 */
interface AccessCredentialInterface
{
    // Chaque credential peut avoir un type différent pour que la logique de
    // notification puisse s'adapter.
    public function getType(): string;
}
