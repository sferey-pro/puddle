<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Repository;

use App\Module\Auth\Domain\OneTimePasswordAttempt;
use App\Module\Auth\Domain\PasswordResetRequest;
use App\Module\Auth\Domain\ValueObject\OtpAttemptId;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;

/**
 * Port de persistance pour les demandes de réinitialisation de mot de passe.
 */
interface OtpAttemptRepositoryInterface
{
    public function save(OneTimePasswordAttempt $attempt): void;

    public function ofId(OtpAttemptId $id): ?OneTimePasswordAttempt;
}
