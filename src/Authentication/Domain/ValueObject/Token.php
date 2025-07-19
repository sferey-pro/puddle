<?php

declare(strict_types=1);

namespace Authentication\Domain\ValueObject;

interface Token
{
    /**
     * La valeur brute du token (le secret)
     */
    public function value(): string;

    /**
     * Le type de token pour la discrimination
     */
    public function type(): string;

    /**
     * Vérifie si le token correspond à une valeur donnée
     */
    public function matches(string $value): bool;

    /**
     * Vérifie si le token a expiré
     */
    public function isExpired(\DateTimeImmutable $now): bool;

    /**
     * Date d'expiration du token
     */
    public function expiresAt(): \DateTimeImmutable;

    /**
     * Représentation sécurisée pour les logs (masquée)
     */
    public function __toString(): string;
}
