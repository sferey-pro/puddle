<?php

declare(strict_types=1);

namespace SharedKernel\Presentation\Twig\Extension;

use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Fournit des filtres Twig pour afficher des durées de manière lisible.
 */
final class HumanReadableDurationExtension extends AbstractExtension
{
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('human_duration', [$this, 'formatHumanDuration']),
        ];
    }

    /**
     * Transforme une date future en une chaîne de caractères lisible
     * indiquant le temps restant (ex: "dans 5 heures").
     */
    public function formatHumanDuration(\DateTimeInterface $date): string
    {
        $now = new \DateTime();

        // S'assure que la date est dans le futur
        if ($date <= $now) {
            return $this->translator->trans('now');
        }

        $interval = $date->diff($now);

        // Toute la logique de la méthode getExpirationMessageKey() est déplacée ici
        switch (true) {
            case $interval->y > 0:
                return $this->translator->trans('%count% year|%count% years', ['%count%' => $interval->y]);
            case $interval->m > 0:
                return $this->translator->trans('%count% month|%count% months', ['%count%' => $interval->m]);
            case $interval->d > 0:
                return $this->translator->trans('%count% day|%count% days', ['%count%' => $interval->d]);
            case $interval->h > 0:
                return $this->translator->trans('%count% hour|%count% hours', ['%count%' => $interval->h]);
            default:
                // Pour les minutes, on s'assure de ne pas retourner 0
                $minutes = $interval->i ?: 1;

                return $this->translator->trans('%count% minute|%count% minutes', ['%count%' => $minutes]);
        }
    }
}
