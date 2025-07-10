<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\Otp;

use App\Core\Application\Command\CommandInterface;
use App\Module\Auth\Domain\ValueObject\OtpAttemptId;
use App\Module\Auth\Domain\ValueObject\UserIdentity;

final readonly class SendOtpNotification implements CommandInterface
{
    public function __construct(
        public OtpAttemptId $id,
        public UserIdentity $identity,
        public string $plainOtpCode
    ) {
    }
}
