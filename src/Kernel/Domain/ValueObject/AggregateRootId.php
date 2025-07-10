<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObject;

/**
 * Classe de base abstraite pour tous les identifiants d'agrégat.
 * Fournit une base commune et permet le typage polymorphique.
 * Un UserId, un OrderId, etc. doivent hériter de cette classe.
 */
abstract class AggregateRootId extends AbstractUid
{
    
}
