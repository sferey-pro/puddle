<?php

declare(strict_types=1);

namespace App\Shared\UI\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

/**
 * Composant Twig générique pour afficher des données sous forme de tableau.
 *
 * Ce composant est conçu pour être hautement réutilisable. Il accepte une liste
 * d'items et une configuration de colonnes pour générer dynamiquement un tableau
 * avec en-têtes, lignes et actions.
 */
#[AsTwigComponent()]
final class Table
{
    /**
     * Le titre affiché au-dessus du tableau.
     */
    public ?string $title = null;

    /**
     * La liste des objets ou tableaux à afficher.
     * @var iterable<object|array>
     */
    public iterable $items = [];

    /**
     * La configuration des colonnes.
     * Ex: [['label' => 'Nom', 'path' => 'name'], ['label' => 'Prix', 'path' => 'price.amount', 'format' => 'currency']]
     * @var array<array{label: string, path: string, format?: string}>
     */
    public array $columns = [];

    /**
     * La configuration des actions pour chaque ligne.
     * Ex: [['route' => 'user_edit', 'icon' => 'tabler:edit']]
     * @var array<array{route: string, icon: string, title: string}>
     */
    public array $actions = [];
}
