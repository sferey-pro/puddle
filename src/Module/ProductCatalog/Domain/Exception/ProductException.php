<?php

declare(strict_types=1);

namespace App\Module\ProductCatalog\Domain\Exception;

use App\Module\SharedContext\Domain\ValueObject\ProductId;

final class ProductException extends \DomainException
{
    public const NOT_FOUND = 'CP-001';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null,  \Throwable|null $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFoundWithId(ProductId $id, \Throwable|null $previous = null): self
    {
        $message = \sprintf('Product with ID "%s" was not found.', $id);
        return new self($message, self::NOT_FOUND, ['id' => $id], $previous);
    }

    public function payload(): mixed
    {
        return $this->payload;
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
