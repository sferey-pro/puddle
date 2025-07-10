<?php

declare(strict_types=1);

namespace Account\Registration\Infrastructure\Persistence\Doctrine\Repository;

use Account\Registration\Domain\Repository\RegistrationProcessRepositoryInterface;
use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kernel\Domain\Saga\SagaStateId;
use Kernel\Infrastructure\Persistence\Doctrine\ORMAbstractRepository;

/**
 * Implémentation Doctrine du repository pour les processus de saga d'inscription.
 *
 * @extends ServiceEntityRepository<RegistrationSagaProcess>
 */
final class DoctrineRegistrationProcessRepository extends ORMAbstractRepository implements RegistrationProcessRepositoryInterface
{
    private const ENTITY_CLASS = RegistrationSagaProcess::class;
    private const ALIAS = 'registration_process';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, self::ENTITY_CLASS, self::ALIAS);
    }

    public function ofId(SagaStateId $id): ?RegistrationSagaProcess
    {
        return parent::find($id);
    }

    public function save(RegistrationSagaProcess $process): void
    {
        $this->getEntityManager()->persist($process);
    }
}
