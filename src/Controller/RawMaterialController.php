<?php

declare(strict_types=1);

namespace App\Controller;

use App\Common\Command\CommandBusInterface;
use App\Entity\RawMaterial;
use App\Form\RawMaterialType;
use App\Repository\RawMaterialRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/raw-material')]
final class RawMaterialController extends AbstractController
{
    #[Route(name: 'app_raw_material_index', methods: ['GET'])]
    public function index(RawMaterialRepository $rawMaterialRepository): Response
    {
        return $this->render('raw_material/index.html.twig', [
            'raw_materials' => $rawMaterialRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_raw_material_new', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return $this->render('raw_material/new.html.twig');
    }

    #[Route('/{id}', name: 'app_raw_material_show', methods: ['GET'])]
    public function show(RawMaterial $rawMaterial): Response
    {
        return $this->render('raw_material/show.html.twig', [
            'raw_material' => $rawMaterial,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_raw_material_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RawMaterial $rawMaterial, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RawMaterialType::class, $rawMaterial);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_raw_material_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('raw_material/edit.html.twig', [
            'raw_material' => $rawMaterial,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_raw_material_delete', methods: ['POST'])]
    public function delete(Request $request, RawMaterial $rawMaterial, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rawMaterial->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rawMaterial);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_raw_material_index', [], Response::HTTP_SEE_OTHER);
    }
}
