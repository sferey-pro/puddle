<?php

declare(strict_types=1);

namespace SharedKernel\Presentation\Twig\Components\Business;

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
     * @param mixed|null  $value      La donnée brute (bool, string...)
     * @param string|null $label      forcer un libellé spécifique
     * @param string|null $color      forcer une couleur spécifique
     * @param string|null $enumType   la classe de l'Enum à utiliser pour interpréter $value
     * @param string      $variant    style du badge ('light' ou 'solid')
     * @param bool        $dot        afficher un point de statut
     * @param string|null $icon       afficher une icône
     * @param string      $trueLabel  libellé pour le booléen TRUE
     * @param string      $falseLabel libellé pour le booléen FALSE
     * @param string      $trueColor  couleur pour le booléen TRUE
     * @param string      $falseColor couleur pour le booléen FALSE
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
        string $falseColor = 'red',
    ): void {
        // On assigne d'abord les props de style qui ne dépendent pas de la logique.
        $this->variant = $variant;
        $this->dot = $dot;
        $this->icon = $icon;

        // On initialise les variables de travail
        $finalLabel = $label;
        $finalColor = $color;

        // 1. Logique pour les Enums
        if ($enumType && \is_string($value) && enum_exists($enumType)) {
            $enumCase = $enumType::tryFrom($value);
            if ($enumCase && method_exists($enumCase, 'getBadgeConfiguration')) {
                $config = $enumCase->getBadgeConfiguration();
                $finalLabel = $label ?? $config['label'] ?? $value;
                $finalColor = $color ?? $config['color'] ?? 'gray';
            }
        }

        // 2. Logique pour les Booléens
        elseif (\is_bool($value)) {
            $finalLabel = $value ? $trueLabel : $falseLabel;
            $finalColor = $value ? $trueColor : $falseColor;
        }

        // On assigne les valeurs finales aux propriétés publiques de la classe
        // pour que le template puisse les utiliser.
        $this->label = $finalLabel ?? ($value instanceof \UnitEnum ? $value->name : (string) $value);
        $this->color = $finalColor ?? 'gray';
    }
}
