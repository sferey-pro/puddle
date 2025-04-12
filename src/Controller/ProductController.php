<?php

namespace App\Controller;

use App\Common\Command\CommandBusInterface;
use App\Entity\Product;
use App\Entity\RawMaterialList;
use App\Form\ProductFormType;
use App\Form\RawMaterialListFormType;
use App\Messenger\Command\Product\NewProduct;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/product')]
final class ProductController extends AbstractController{

    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return $this->render('product/new.html.twig');
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $rawMaterialList = new RawMaterialList();
        $form = $this->createForm(RawMaterialListFormType::class, $rawMaterialList);


        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form,
            'rawMaterialList' => $rawMaterialList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/duplicate', name: 'app_product_duplicate', methods: ['POST'])]
    public function duplicate(Request $request, Product $product, CommandBusInterface $commandBus): Response
    {
        if ($this->isCsrfTokenValid('duplicate'.$product->getId(), $request->getPayload()->getString('_token'))) {

            $commandBus->dispatch(new NewProduct(
                name: $product->getName(),
                price: $product->getPrice(),
                category: $product->getCategory(),
            ));

            $this->addFlash('live_demo_success', 'Product duplicated!');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
