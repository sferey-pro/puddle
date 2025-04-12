<?php

declare(strict_types=1);

namespace App\Messenger\Command\RawMaterial;

use App\Entity\RawMaterial;
use App\Messenger\Attribute\AsCommandHandler;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommandHandler]
final class NewRawMaterialHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(NewRawMaterial $command): void
    {
        $product = new RawMaterial();
        $product->setName($command->getName());
        $product->setUnitPrice($command->getUnitPrice());
        $product->setSupplier($command->getSupplier());
        $product->setPriceVariability($command->isPriceVariability());
        $product->setCategory($command->getCategory());
        $product->setUnit($command->getUnit());
        $product->setTotalCost($command->getTotalCost());
        $product->setNotes($command->getNotes());

        $this->em->persist($product);
        $this->em->flush();
    }
}
