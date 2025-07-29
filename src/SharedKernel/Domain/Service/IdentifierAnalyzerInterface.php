<?php

declare(strict_types=1);

namespace SharedKernel\Domain\Service;

use SharedKernel\Domain\DTO\Identity\IdentifierAnalysis;

/**
 * Contrat public pour l'analyse des identifiants.
 *
 * RESPONSABILITÉ UNIQUE : Analyser un string et extraire toutes les
 * métadonnées nécessaires aux autres contextes (type, masquage, canal, etc.)
 *
 * APPARTENANCE : L'implémentation appartient au contexte Identity car c'est
 * lui qui connaît les règles métier des identifiants.
 */
interface IdentifierAnalyzerInterface
{
    /**
     * Analyse un identifiant brut et retourne toutes ses métadonnées.
     *
     * @param string $rawIdentifier L'identifiant tel que saisi par l'utilisateur
     * @return IdentifierAnalysis Contient : type, validité, valeur normalisée, masquée, canal, etc.
     */
    public function analyze(string $rawIdentifier): IdentifierAnalysis;
}
