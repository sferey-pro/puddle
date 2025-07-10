<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Otp;

use App\Module\Auth\Domain\ValueObject\UserIdentity;

final readonly class VerifyOtpCommand
{
    public function __construct(
        public UserIdentity $identity,
        public string $submittedOtpCode
    ) {
    }
}
