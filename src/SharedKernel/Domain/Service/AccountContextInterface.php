<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;

use Account\Core\Domain\Model\Account;
use SharedKernel\Domain\DTO\Account\AccountStatusDTO;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Interface publique du contexte Account
 * Définit le contrat pour les autres Bounded Contexts
 */
interface AccountContextInterface
{
    /**
     * Récupère un compte par son ID.
     */
    public function ofId(UserId $userId): ?Account;

    /**
     * Informations de statut pour Authentication
     */
    public function getAccountStatus(UserId $userId): ?AccountStatusDTO;

    /**
     * Vérification simple d'existence
     */
    public function accountExists(UserId $userId): bool;

    /**
     * Vérifications métier spécifiques
     */
    public function canAuthenticate(UserId $userId): bool;
    public function hasReachedLoginLimit(UserId $userId): bool;
}
