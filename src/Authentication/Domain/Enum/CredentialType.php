<?php

declare(strict_types=1);

namespace Authentication\Domain\Enum;

enum CredentialType: string
{
    case MAGIC_LINK = 'magic_link';
    case OTP = 'otp';
    case PASSWORD = 'password';
    case OAUTH = 'oauth';

    
}
