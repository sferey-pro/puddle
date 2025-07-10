<?php

declare(strict_types=1);

namespace Identity\Domain\ValueObject;

use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Identifiant propre au contexte Identity.
 * Représente une instance d'attachement d'identité.
 */
final class AttachedIdentifierId extends AggregateRootId
{
}
