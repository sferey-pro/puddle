<?php

declare(strict_types=1);

namespace Identity\Domain\Model\Identity;

use Kernel\Domain\ValueObject\AggregateRootId;

/**
 * Identifiant propre au contexte Identity.
 * Représente une instance d'attachement d'identité.
 */
final class AttachedIdentifierId extends AggregateRootId
{
}
