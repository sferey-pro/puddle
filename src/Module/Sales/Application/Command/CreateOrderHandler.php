<?php

declare(strict_types=1);

namespace App\Module\Sales\Application\Command;

use App\Module\ProductCatalog\Application\Query\FindProductQuery;
use App\Module\Sales\Domain\Exception\OrderExceptionACLFactory;
use App\Module\Sales\Domain\Order;
use App\Module\Sales\Domain\OrderLine;
use App\Module\Sales\Domain\Repository\OrderRepositoryInterface;
use App\Module\SharedContext\Domain\ValueObject\ProductId;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Shared\Application\Event\EventBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;

#[AsCommandHandler]
final class CreateOrderHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $repository,
        private readonly QueryBusInterface $queryBus,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateOrder $command): void
    {
        $dto = $command->createOrderDTO;

        $orderLines = [];
        foreach ($dto->orderLines as $lineDto) {
            $productId = ProductId::fromString($lineDto->productId);

            try {
                $product = $this->queryBus->ask(new FindProductQuery($productId));
            } catch (\DomainException $th) {
                throw OrderExceptionACLFactory::fromProductException($th);
            }

            // NOTE: Dans un vrai projet, le prix viendrait d'une source fiable (le produit, un service de pricing, etc.)
            // Ici on utilise le prix de base du produit pour la démo.
            /** @var Product $product */
            $price = $product->baseCostStructure()->totalBaseCost();

            $orderLines[] = OrderLine::create(
                ProductId::fromString($lineDto->productId),
                $lineDto->quantity,
                $price
            );
        }

        $order = Order::create(UserId::fromString($dto->userId), $orderLines);

        $this->repository->save($order);
        $this->eventBus->publish(...$order->pullDomainEvents());
    }
}
