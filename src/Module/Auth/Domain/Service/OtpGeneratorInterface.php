<?php

declare(strict_types=1);

namespace App\Auth\Domain\Service;

/**
 * Contrat pour la génération d'un code OTP en clair.
 */
interface OtpGeneratorInterface
{
    /**
     * Génère un code OTP à 6 chiffres.
     */
    public function generate(): string;
}
