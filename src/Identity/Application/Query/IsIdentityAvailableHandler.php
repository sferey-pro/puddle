<?php

declare(strict_types=1);

namespace Identity\Application\Query;

use Identity\Domain\Repository\UserIdentityRepositoryInterface;
use Identity\Domain\Specification\IsUniqueIdentitySpecification;
use Kernel\Infrastructure\Symfony\Messenger\Attribute\AsQueryHandler;

#[AsQueryHandler]
final readonly class IsIdentityAvailableHandler
{
    public function __construct(
        private UserIdentityRepositoryInterface $userIdentityRepository
    ) {
    }

    public function __invoke(IsIdentityAvailable $query): bool
    {
        // 1. On crée la spécification du domaine Identity
        $spec = new IsUniqueIdentitySpecification($query->identifier);

        // 2. On demande au repository de compter les résultats correspondants
        // Le repository utilisera l'adaptateur Doctrine pour traduire la spec en requête.
        $count = $this->userIdentityRepository->countBySpecification($spec);

        // 3. La logique est simple et claire
        return 0 === $count;
    }
}
