<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Command;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\DTO\CreateUserDTO;
use App\Shared\Saga\Application\SagaActionCommandInterface;

final readonly class CreateUser implements SagaActionCommandInterface
{
    public function __construct(
        public CreateUserDTO $dto,
        public ?UserId $userId = null,
    ) {
    }

    public function getCorrelationId(): UserId
    {
        return $this->userId;
    }
}
