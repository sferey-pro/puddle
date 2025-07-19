<?php

declare(strict_types=1);

namespace Content\StaticContent\Controller;

use Content\StaticContent\Repository\StaticContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pages', name: 'static_')]
final class StaticPageController extends AbstractController
{
    public function __construct(
        private readonly StaticContentRepository $contentRepository
    ) {}

    #[Route('/cgv', name: 'cgv', methods: ['GET'])]
    public function cgv(): Response
    {
        return $this->renderStaticPage('cgv');
    }

    #[Route('/faq', name: 'faq', methods: ['GET'])]
    public function faq(): Response
    {
        return $this->renderStaticPage('faq');
    }

    #[Route('/under-construction', name: 'under_construction', methods: ['GET'])]
    public function underConstruction(): Response
    {
        return $this->renderStaticPage('under_construction');
    }

    private function renderStaticPage(string $pageKey): Response
    {
        $pageConfig = $this->contentRepository->getPageConfig($pageKey);

        return $this->render($pageConfig['template'], [
            'page_title' => $pageConfig['title'],
            'meta_description' => $pageConfig['meta_description'] ?? null,
        ]);
    }
}
