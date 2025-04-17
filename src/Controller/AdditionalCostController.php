<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AdditionalCost;
use App\Form\AdditionalCostType;
use App\Repository\AdditionalCostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/additional/cost')]
final class AdditionalCostController extends AbstractController
{
    #[Route(name: 'app_additional_cost_index', methods: ['GET'])]
    public function index(AdditionalCostRepository $additionalCostRepository): Response
    {
        return $this->render('additional_cost/index.html.twig', [
            'additional_costs' => $additionalCostRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_additional_cost_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $additionalCost = new AdditionalCost();
        $form = $this->createForm(AdditionalCostType::class, $additionalCost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($additionalCost);
            $entityManager->flush();

            return $this->redirectToRoute('app_additional_cost_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('additional_cost/new.html.twig', [
            'additional_cost' => $additionalCost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_additional_cost_show', methods: ['GET'])]
    public function show(AdditionalCost $additionalCost): Response
    {
        return $this->render('additional_cost/show.html.twig', [
            'additional_cost' => $additionalCost,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_additional_cost_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdditionalCost $additionalCost, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdditionalCostType::class, $additionalCost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_additional_cost_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('additional_cost/edit.html.twig', [
            'additional_cost' => $additionalCost,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_additional_cost_delete', methods: ['POST'])]
    public function delete(Request $request, AdditionalCost $additionalCost, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$additionalCost->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($additionalCost);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_additional_cost_index', [], Response::HTTP_SEE_OTHER);
    }
}
