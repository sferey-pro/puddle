// src/Module/Auth/Infrastructure/Doctrine/Repository/DoctrinePasswordCredentialRepository.php
<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\PasswordCredential;
use App\Module\Auth\Domain\Repository\PasswordCredentialRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class DoctrinePasswordCredentialRepository extends ServiceEntityRepository implements PasswordCredentialRepositoryInterface
{
    private const ENTITY_CLASS = PasswordCredential::class;
    private const ALIAS = 'password_credential';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function ofUserId(UserId $id): ?PasswordCredential
    {
        return parent::find($id);
    }

    public function save(PasswordCredential $credential): void
    {
        $this->_em->persist($credential);
        $this->_em->flush();
    }
}
