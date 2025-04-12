<?php

namespace App\Repository;

use App\Entity\RawMaterialList;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ResourceListRepository<RawMaterialList>
 */
class RawMaterialListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RawMaterialList::class);
    }
}
