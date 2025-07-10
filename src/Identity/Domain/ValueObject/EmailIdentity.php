<?php

declare(strict_types=1);

namespace Identity\Domain\ValueObject;

use Kernel\Domain\Result;
use Kernel\Domain\ValueObject\ValueObjectInterface;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;

final readonly class EmailIdentity implements Identifier
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

    public function equals(Identifier $other): bool
    {
        return $other->getClass() instanceof $this->email && $this->email->equals($other->email);
    }

    public function getClass(): string
    {
        return EmailAddress::class;
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
