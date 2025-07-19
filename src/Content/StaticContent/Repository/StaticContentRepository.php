<?php

declare(strict_types=1);

namespace Content\StaticContent\Repository;

use Content\StaticContent\Exception\ContentNotFoundException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Yaml\Yaml;

final readonly class StaticContentRepository
{
    private array $config;

    public function __construct(
        #[Autowire('%kernel.project_dir%/src/Content/config/static_pages.yaml')]
        private string $configPath
    ) {
        $this->config = Yaml::parseFile($this->configPath);
    }

    /**
     * @return array{template: string, title: string, meta_description?: string, ...}
     */
    public function getPageConfig(string $pageKey): array
    {
        if (!isset($this->config['pages'][$pageKey])) {
            throw ContentNotFoundException::forPage($pageKey);
        }

        // Merge avec les defaults
        return array_merge(
            $this->config['defaults'] ?? [],
            $this->config['seo_defaults'] ?? [],
            $this->config['pages'][$pageKey]
        );
    }

    /**
     * Récupère toutes les pages pour génération de sitemap
     * @return array<string, array>
     */
    public function getAllPagesForSitemap(): array
    {
        return array_filter(
            $this->config['pages'],
            fn(array $page) => ($page['robots'] ?? '') !== 'noindex, nofollow'
        );
    }
}
