<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\ValueObject;

use App\Core\Domain\Result;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;

final readonly class EmailIdentity implements UserIdentity
{
    private(set) EmailAddress $email;

    private function __construct(EmailAddress $email)
    {
        $this->email = $email;
    }

    /**
     * @return Result<self>
     */
    public static function create(string $emailValue): Result
    {
        $emailAddressResult = EmailAddress::create($emailValue);

        if ($emailAddressResult->isFailure()) {
            return $emailAddressResult;
        }

        $identity = new self($emailAddressResult->value());

        return Result::success($identity);
    }

    public function value(): string
    {
        return $this->email->value;
    }

    public static function uniqueFieldPath(): string
    {
        return 'email';
    }

    public function uniqueValue(): string
    {
        return $this->value();
    }
}
