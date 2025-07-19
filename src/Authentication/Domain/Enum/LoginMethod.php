<?php

declare(strict_types=1);

namespace Authentication\Domain\Enum;

enum LoginMethod: string
{
    case PASSWORD = 'password';
    case MAGIC_LINK = 'magic_link';
    case OAUTH_GOOGLE = 'oauth_google';
    case OAUTH_GITHUB = 'oauth_github';
    case API_KEY = 'api_key';
    case SSO = 'sso';
    case BIOMETRIC = 'biometric';

    public function requiresPassword(): bool
    {
        return $this === self::PASSWORD;
    }

    public function isOAuth(): bool
    {
        return str_starts_with($this->value, 'oauth_');
    }

    public function isTrusted(): bool
    {
        return in_array($this, [self::SSO, self::BIOMETRIC], true);
    }
}
