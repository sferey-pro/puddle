<?php

declare(strict_types=1);

namespace Identity\Domain\Exception;

use Identity\Domain\ValueObject\Identifier;
use SharedKernel\Domain\ValueObject\Identity\UserId;

final class InvalidIdentifierException extends \DomainException
{

}
