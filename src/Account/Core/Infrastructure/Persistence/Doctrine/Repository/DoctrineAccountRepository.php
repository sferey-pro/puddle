<?php

namespace Account\Core\Infrastructure\Persistence\Doctrine\Repository;

use Account\Core\Domain\Account;
use Account\Core\Domain\Repository\AccountRepositoryInterface;
use SharedKernel\Domain\ValueObject\Identity\UserId;
use Account\Registration\Domain\Repository\RegistrationRepositoryInterface;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Kernel\Domain\Specification\SpecificationInterface;
use Doctrine\Persistence\ManagerRegistry;
use Kernel\Infrastructure\Persistence\Doctrine\ORMAbstractRepository;
use Kernel\Infrastructure\Specification\DoctrineSpecificationAdapter;
use SharedKernel\Domain\ValueObject\Contact\EmailAddress;
use SharedKernel\Domain\ValueObject\Contact\PhoneNumber;

final class DoctrineAccountRepository extends ORMAbstractRepository implements AccountRepositoryInterface, RegistrationRepositoryInterface
{
    private const ENTITY_CLASS = Account::class;
    private const ALIAS = 'account';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(Account $account): void
    {
        $this->em->persist($account);
    }

    public function remove(Account $account): void
    {
        $this->em->remove($account);
    }

    public function ofId(UserId $id): ?Account
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function ofEmail(EmailAddress $email): ?Account
    {
        $qb = $this->withEmail($email)
            ->query();

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    private function withEmail(EmailAddress $email): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($email): void {
            $qb->where(\sprintf('%s.email = :email', self::ALIAS))->setParameter('email', $email);
        });
    }

    public function ofPhone(PhoneNumber $phone): ?Account
    {
        $qb = $this->withPhone($phone)
            ->query();

        return $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
    }

    private function withPhone(PhoneNumber $phone): ?self
    {
        return $this->filter(static function (QueryBuilder $qb) use ($phone): void {
            $qb->where(\sprintf('%s.phone = :phone', self::ALIAS))->setParameter('phone', $phone);
        });
    }

    /**
     * Trouve les entités matchant une Specification.
     */
    public function matching(SpecificationInterface $specification): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Account::class, 'a');

        // Si c'est une specification Doctrine, on l'applique au QueryBuilder
        if ($specification instanceof DoctrineSpecificationAdapter) {
            $specification->modifyQuery($qb, 'a');
        } else {
            // Sinon, on charge tout et on filtre en mémoire (moins performant)
            $results = $qb->getQuery()->getResult();
            return array_filter(
                $results,
                fn($account) => $specification->isSatisfiedBy($account)
            );
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte les entités matchant une Specification.
     */
    public function count(SpecificationInterface $specification): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(a.id)')
            ->from(Account::class, 'a');

        if ($specification instanceof DoctrineSpecificationAdapter) {
            $specification->modifyQuery($qb, 'a');
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
