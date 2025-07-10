<?php

declare(strict_types=1);

namespace SharedKernel\Domain\ValueObject\Identity;

use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Identifiant universel d'une personne dans le système.
 * Partagé entre tous les contextes pour maintenir la cohérence.
 * Représente l'identité stable de la personne, indépendamment
 * de ses comptes, identifiants ou sessions.
 */
final class UserId extends AggregateRootId
{

}
