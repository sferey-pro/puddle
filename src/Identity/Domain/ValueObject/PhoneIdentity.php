<?php

declare(strict_types=1);

namespace Identity\Domain\ValueObject;

use Kernel\Domain\Result;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;

final readonly class PhoneIdentity implements Identifier
{
    private(set) PhoneNumber $phone;

    private function __construct(PhoneNumber $phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return Result<self>
     */
    public static function create(string $phoneValue): Result
    {
        $phoneNumberResult = PhoneNumber::create($phoneValue);

        if ($phoneNumberResult->isFailure()) {
            return $phoneNumberResult;
        }

        $identity = new self($phoneNumberResult->value());

        return Result::success($identity);
    }

    public function value(): string
    {
        return $this->phone->value;
    }

    public function equals(Identifier $other): bool
    {
        return $other->getClass() instanceof $this->phone && $this->phone->equals($other->phone);
    }

    public function getClass(): string
    {
        return PhoneNumber::class;
    }

    public function getType(): string
    {
        return 'phone';
    }

    public static function uniqueFieldPath(): string
    {
        return 'phone';
    }

    public function uniqueValue(): string
    {
        return $this->value();
    }
}
