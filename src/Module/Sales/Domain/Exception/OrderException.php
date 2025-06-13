<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Exception;

use App\Module\Sales\Domain\ValueObject\OrderId;
use App\Module\SharedContext\Domain\ValueObject\ProductId;

/**
 * Exception de base pour toutes les erreurs métier liées à l'agrégat Order.
 * Cette classe utilise des constructeurs statiques nommés pour fournir des
 * messages d'erreur contextuels et clairs, tout en réduisant le nombre de classes d'exception.
 */
final class OrderException extends \DomainException
{
    public const NOT_FOUND = 'SA-001';
    public const PRODUCT_NOT_FOUND = 'SA-002';
    public const PRODUCT_INVALID = 'SA-003';

    /**
     * Le constructeur est privé pour forcer l'utilisation des factory methods statiques.
     */
    private function __construct(string $message, private string $errorCode, private mixed $payload = null,  \Throwable|null $previous = null)
    {
        parent::__construct(message: $message, previous: $previous);
    }

    public static function notFoundWithId(OrderId $id): self
    {
        return new self(\sprintf('CostItem with ID "%s" was not found.', $id), self::NOT_FOUND);
    }

    public static function productNotFound(ProductId $id, \Throwable|null $previous = null): self
    {
        $message =  \sprintf('Product with ID "%s" was not found in this aggregate.', $id);
        return new self($message, self::NOT_FOUND, ['id' => $id], $previous);
    }

    public static function invalidProduct(ProductId $id, \Throwable|null $previous): self
    {
        $message =  \sprintf('Product with ID "%s" is invalid.', $id);
        return new self($message, self::PRODUCT_INVALID, ['id' => $id], $previous);
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
