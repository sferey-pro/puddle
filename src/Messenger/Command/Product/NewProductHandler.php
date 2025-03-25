<?php

declare(strict_types=1);

namespace App\Messenger\Command\Product;

use App\Entity\Product;
use App\Messenger\Attribute\AsCommandHandler;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommandHandler]
final class NewProductHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(NewProduct $command): void
    {
        $product = new Product();
        $product->setName($command->getName());
        $product->setPrice($command->getPrice());
        $product->setCategory($command->getCategory());

        $this->em->persist($product);
        $this->em->flush();
    }
}
