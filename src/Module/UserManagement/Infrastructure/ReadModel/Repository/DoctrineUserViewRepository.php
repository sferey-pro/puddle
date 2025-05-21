<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\ReadModel\Repository;

use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Shared\Infrastructure\Doctrine\ODMAbstractRepository;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class DoctrineUserViewRepository extends ODMAbstractRepository implements UserViewRepositoryInterface
{
    private const DOCUMENT_CLASS = UserView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function findById(UserId $identifier): ?UserView
    {
        return parent::findOneBy(['userId' => $identifier->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }
}
