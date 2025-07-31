<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;

use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\DTO\Identity\UserIdentifiersDTO;
use SharedKernel\Domain\ValueObject\Identity\UserId;

interface IdentityContextInterface
{
    public function getUserIdentifiers(UserId $userId): ?UserIdentifiersDTO;

    public function findUserIdByIdentifier(string $identifierValue): ?UserId;

    // ===== RÉSOLUTION BASIQUE (EXISTANTE) =====
    /**
     * Résout un identifiant et lance une exception si invalide.
     *
     * USAGE : Quand on VEUT une exception en cas d'échec (cas normal).
     * C'est la méthode que les Handlers utiliseront principalement.
     */
    public function resolveIdentifierorThrow(string $value): Identifier;

    /**
     * Résolution "safe" qui retourne null au lieu d'exception.
     *
     * USAGE : Quand on veut tester la validité sans lever d'exception.
     * Utile pour la validation optionnelle ou les batch.
     */
    public function tryResolveIdentifier(string $value): ?Identifier;

    /**
     * Batch resolution pour plusieurs identifiants.
     *
     * USAGE : Import de données, traitement en lot.
     * Retourne seulement les identifiants valides.
     */
    public function resolveMultiple(array $rawIdentifiers): array;
}
