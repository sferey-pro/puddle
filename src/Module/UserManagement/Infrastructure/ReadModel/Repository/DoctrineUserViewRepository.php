<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Infrastructure\ReadModel\Repository;

use App\Core\Infrastructure\Persistence\ODMAbstractRepository;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use Doctrine\Bundle\MongoDBBundle\ManagerRegistry;

class DoctrineUserViewRepository extends ODMAbstractRepository implements UserViewRepositoryInterface
{
    private const DOCUMENT_CLASS = UserView::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::DOCUMENT_CLASS);
    }

    public function findById(UserId $id): ?UserView
    {
        return parent::findOneBy(['id' => $id->value]);
    }

    public function findAll(): array
    {
        return parent::findAll();
    }

    public function add(UserView $user): void
    {
        $this->getDocumentManager()->persist($user);
    }

    public function save(UserView $user, bool $flush = false): void
    {
        $this->add($user);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function delete(UserView $user, bool $flush = false): void
    {
        $this->remove($user);

        if ($flush) {
            $this->getDocumentManager()->flush();
        }
    }

    public function remove(UserView $user): void
    {
        $this->getDocumentManager()->remove($user);
    }
}
