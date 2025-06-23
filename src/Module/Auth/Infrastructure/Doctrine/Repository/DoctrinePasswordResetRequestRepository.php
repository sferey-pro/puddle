<?php

declare(strict_types=1);

namespace App\Module\Auth\Infrastructure\Doctrine\Repository;

use App\Module\Auth\Domain\PasswordResetRequest;
use App\Module\Auth\Domain\Repository\PasswordResetRequestRepositoryInterface;
use App\Module\Auth\Domain\ValueObject\PasswordResetRequestId;
use App\Module\SharedContext\Domain\ValueObject\Email;
use App\Shared\Domain\Service\SystemTime;
use App\Shared\Infrastructure\Doctrine\ORMAbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Implémentation Doctrine du repository pour les demandes de réinitialisation de mot de passe.
 * Gère la persistance et la récupération des agrégats PasswordResetRequest.
 */
final class DoctrinePasswordResetRequestRepository extends ORMAbstractRepository implements PasswordResetRequestRepositoryInterface
{
    private const ENTITY_CLASS = PasswordResetRequest::class;
    private const ALIAS = 'password_reset_request';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function save(PasswordResetRequest $request, bool $flush = false): void
    {
        $this->add($request);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function add(PasswordResetRequest $request): void
    {
        $this->getEntityManager()->persist($request);
    }

    public function ofId(PasswordResetRequestId $id): ?PasswordResetRequest
    {
        return $this->findOneBy(['id.value' => $id->value]);
    }

    public function ofSelector(string $selector): ?PasswordResetRequest
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function countRecentRequests(Email $email): int
    {
        $count = $this->createQueryBuilder('r')
            ->select('COUNT(r.id.value)')
            ->where('r.requestedEmail.value = :email')
            ->andWhere('r.expiresAt > :now')
            ->setParameter('email', $email->value)
            ->setParameter('now', SystemTime::now())
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count;
    }

    public function findOldestNonExpiredRequestDate(Email $email): ?\DateTimeImmutable
    {
        $result = $this->createQueryBuilder('r')
            ->select('MIN(r.expiresAt)')
            ->where('r.requestedEmail.value = :email')
            ->andWhere('r.expiresAt > :now')
            ->setParameter('email', $email->value)
            ->setParameter('now', SystemTime::now())
            ->getQuery()
            ->getSingleScalarResult();

        return $result ? new \DateTimeImmutable($result) : null;
    }

    public function deleteExpiredOlderThan(\DateTimeImmutable $threshold): int
    {
        $query = $this->createQueryBuilder('r')
            ->delete(self::ENTITY_CLASS, 'r')
            ->where('r.expiresAt < :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery();

        return $query->execute();
    }
}
