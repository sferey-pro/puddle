<?php

declare(strict_types=1);

namespace App\Core\Application\Saga;

use App\Core\Domain\Saga\Process\AbstractSagaProcess;
use App\Core\Domain\Saga\SagaStateId;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SagaManager
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public function find(SagaStateId $processId): ?AbstractSagaProcess
    {
        return $this->em->getRepository(AbstractSagaProcess::class)->find($processId);
    }

    public function save(AbstractSagaProcess $process): void
    {
        $this->em->persist($process);
        $this->em->flush();
    }
}
