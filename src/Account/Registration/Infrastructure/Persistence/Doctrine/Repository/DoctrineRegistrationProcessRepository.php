<?php

declare(strict_types=1);

namespace Account\Registration\Infrastructure\Persistence\Doctrine\Repository;

use Account\Registration\Domain\Exception\RegistrationSagaNotFoundException;
use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kernel\Domain\Saga\SagaStateId;
use SharedKernel\Domain\ValueObject\Identity\UserId;

/**
 * Repository Doctrine pour les processus de saga d'inscription.
 *
 * Utilise la stratégie SINGLE_TABLE inheritance via AbstractSagaProcess.
 */
final class DoctrineRegistrationProcessRepository extends ServiceEntityRepository
    implements RegistrationProcessRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegistrationSagaProcess::class);
    }

    // ========== CRUD ==========

    public function save(RegistrationSagaProcess $process): void
    {
        $this->getEntityManager()->persist($process);
        $this->getEntityManager()->flush();
    }

    public function remove(RegistrationSagaProcess $process): void
    {
        $this->getEntityManager()->remove($process);
        $this->getEntityManager()->flush();
    }
}
