<?php

declare(strict_types=1);

namespace Account\Registration\Domain\Service;

use Identity\Domain\Model\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;

/**
 * Interface pour interagir avec Identity depuis Account/Registration
 */
interface IdentifierResolverInterface
{
    public function isEmailAvailable(EmailAddress $email): bool;

    public function isPhoneAvailable(PhoneNumber $phone): bool;

    public function resolveIdentifier(EmailAddress|PhoneNumber $contact): Identifier;
}
