<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command;

use App\Module\Auth\Application\DTO\RegisterUserDTO;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Saga\Application\SagaActionCommandInterface;

final class RegisterUser implements SagaActionCommandInterface
{
    public function __construct(
        public RegisterUserDTO $dto,
        public ?UserId $userId = null,
    ) {
    }

    public function getCorrelationId(): UserId
    {
        return $this->userId;
    }
}
