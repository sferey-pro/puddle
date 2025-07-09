<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Query;

use App\Core\Application\Query\QueryInterface;
use Webmozart\Assert\Assert;

/**
 * Query pour trouver un utilisateur par son identifiant (EmailAddress).
 *
 * @implements QueryInterface<UserAccount>
 */
final readonly class FindUserByIdentifierQuery implements QueryInterface
{
    public string $identifier;

    public function __construct(string $identifier)
    {
        Assert::notEmpty($identifier, 'Identifier cannot be empty.');
        $this->identifier = $identifier;
    }
}
