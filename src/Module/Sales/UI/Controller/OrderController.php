<?php

declare(strict_types=1);

namespace App\Module\Sales\UI\Controller;

use App\Module\Sales\Application\Command\CreateOrder;
use App\Module\Sales\Application\DTO\CreateOrderDTO;
use App\Module\Sales\Application\Query\ListOrdersQuery;
use App\Module\Sales\UI\Form\OrderFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class OrderController extends AbstractController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus
    ) {
    }

    #[Template('@SalesModule/order/index.html.twig')]
    public function index(): array
    {
        $orderItems = $this->queryBus->ask(new ListOrdersQuery());

        return [
            'orderItems' => $orderItems,
        ];
    }

    #[Template('@SalesModule/order/new.html.twig')]
    public function new(Request $request): array|RedirectResponse
    {
        $dto = new CreateOrderDTO();
        $form = $this->createForm(OrderFormType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new CreateOrder($dto));
            $this->addFlash('success', 'La commande a été créée avec succès.');

            return $this->redirectToRoute('sales_order_index');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
