<?php

declare(strict_types=1);

namespace Authentication\Domain\Enum;

enum FailureReason: string
{
    case INVALID_CREDENTIALS = 'invalid_credentials';
    case ACCOUNT_NOT_FOUND = 'account_not_found';
    case ACCOUNT_SUSPENDED = 'account_suspended';
    case ACCOUNT_LOCKED = 'account_locked';
    case EMAIL_NOT_VERIFIED = 'email_not_verified';
    case TWO_FA_REQUIRED = '2fa_required';
    case TWO_FA_FAILED = '2fa_failed';
    case RATE_LIMITED = 'rate_limited';
    case IP_BLOCKED = 'ip_blocked';
    case DEVICE_NOT_TRUSTED = 'device_not_trusted';
    case TOKEN_EXPIRED = 'token_expired';
    case INVALID_TOKEN = 'invalid_token';

    public function isSecurityConcern(): bool
    {
        return in_array($this, [
            self::RATE_LIMITED,
            self::IP_BLOCKED,
            self::DEVICE_NOT_TRUSTED
        ], true);
    }

    public function shouldNotifyUser(): bool
    {
        return in_array($this, [
            self::ACCOUNT_SUSPENDED,
            self::ACCOUNT_LOCKED,
            self::IP_BLOCKED
        ], true);
    }
}
