<?php

namespace Authentication\Infrastructure\Port\Out\DTO;

/**
 * DTO pour les informations Account nécessaires à Authentication.
 *
 * Anti-Corruption Layer : Structure les données selon les besoins
 * du contexte Authentication, indépendamment du modèle Account.
 */
final readonly class AccountInfoDTO
{
    public function __construct(
        public string $userId,
        public string $status,
        public ?string $email,
        public ?string $phone,
        public bool $isVerified,
        public ?\DateTimeImmutable $suspendedUntil
    ) {}
}
