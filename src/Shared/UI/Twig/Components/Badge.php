<?php

declare(strict_types=1);

namespace App\Shared\UI\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
final class Badge
{
    public string $label;
    public string $color;
    public string $variant;
    public bool $dot;
    public ?string $icon;

    /**
     * La méthode mount est le "constructeur" de notre composant.
     *
     * @param mixed|null $value La donnée brute (bool, string...)
     * @param string|null $label Forcer un libellé spécifique.
     * @param string|null $color Forcer une couleur spécifique.
     * @param string|null $enumType La classe de l'Enum à utiliser pour interpréter $value.
     * @param string $variant Style du badge ('light' ou 'solid').
     * @param bool $dot Afficher un point de statut.
     * @param string|null $icon Afficher une icône.
     * @param string $trueLabel Libellé pour le booléen TRUE.
     * @param string $falseLabel Libellé pour le booléen FALSE.
     * @param string $trueColor Couleur pour le booléen TRUE.
     * @param string $falseColor Couleur pour le booléen FALSE.
     */
    public function mount(
        mixed $value = null,
        ?string $label = null,
        ?string $color = null,
        ?string $enumType = null,
        string $variant = 'light',
        bool $dot = false,
        ?string $icon = null,
        string $trueLabel = 'Oui',
        string $falseLabel = 'Non',
        string $trueColor = 'green',
        string $falseColor = 'red'
    ): void {
        // On assigne d'abord les props de style qui ne dépendent pas de la logique.
        $this->variant = $variant;
        $this->dot = $dot;
        $this->icon = $icon;

        // On initialise les variables de travail
        $finalLabel = $label;
        $finalColor = $color;

        // 1. Logique pour les Enums
        if ($enumType && is_string($value) && enum_exists($enumType)) {
            $enumCase = $enumType::tryFrom($value);
            if ($enumCase && method_exists($enumCase, 'getBadgeConfiguration')) {
                $config = $enumCase->getBadgeConfiguration();
                $finalLabel = $label ?? $config['label'] ?? $value;
                $finalColor = $color ?? $config['color'] ?? 'gray';
            }
        }

        // 2. Logique pour les Booléens
        elseif (is_bool($value)) {
            $finalLabel = $value ? $trueLabel : $falseLabel;
            $finalColor = $value ? $trueColor : $falseColor;
        }

        // On assigne les valeurs finales aux propriétés publiques de la classe
        // pour que le template puisse les utiliser.
        $this->label = $finalLabel ?? ($value instanceof \UnitEnum ? $value->name : (string) $value);
        $this->color = $finalColor ?? 'gray';
    }
}
