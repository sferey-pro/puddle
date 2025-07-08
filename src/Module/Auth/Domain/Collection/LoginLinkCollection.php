<?php

declare(strict_types=1);

namespace App\Module\Auth\Domain\Collection;

use App\Core\Domain\Collection\AbstractCollection;
use App\Module\Auth\Domain\LoginLink;
use App\Module\Auth\Domain\ValueObject\Hash;

/**
 * Collection typée pour les objets LoginLink.
 *
 * @extends AbstractCollection<LoginLink>
 */
final class LoginLinkCollection extends AbstractCollection
{
    public function add(LoginLink $loginLink): self
    {
        $items = $this->items;
        $items[] = $loginLink;

        return new self($items);
    }

    public function findByHash(Hash $hash): ?LoginLink
    {
        foreach ($this->items as $item) {
            if ($item->details()->hash->equals($hash)) {
                return $item;
            }
        }

        return null;
    }

    public function filter(callable $callback): self
    {
        return parent::filter($callback);
    }
}
