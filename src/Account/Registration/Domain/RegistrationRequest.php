<?php

declare(strict_types=1);

namespace Account\Registration\Domain;

use SharedKernel\Domain\ValueObject\Identity\UserId;
use Identity\Domain\ValueObject\Identifier;

/**
 * Représente une demande d'inscription validée sémantiquement.
 *
 * C'est un Value Object qui agrège toutes les informations nécessaires
 * pour que les Spécifications puissent prendre une décision.
 * Il est construit dans la couche Application, juste avant d'être
 * passé au "Gardien" (CanRegisterSpecification).
 */
final readonly class RegistrationRequest
{
    /**
     * @param Identifier $identifier L'identité déjà résolue en VO (EmailIdentity ou PhoneIdentity).
     * @param UserId $userId L'ID du compte qui sera créé (peut être généré à l'avance).
     * @param array $metadata Données additionnelles (ex: adresse IP, user agent, etc.)
     */
    public function __construct(
        private(set) Identifier $identifier,
        private(set) UserId $userId,
        private array $metadata = []
    ) {
    }


    public function ipAddress(): ?string
    {
        return $this->metadata['ip_address'] ?? null;
    }
}
