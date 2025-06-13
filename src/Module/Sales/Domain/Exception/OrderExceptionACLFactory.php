<?php

declare(strict_types=1);

namespace App\Module\Sales\Domain\Exception;

use App\Module\ProductCatalog\Domain\Exception\ProductException;


final class OrderExceptionACLFactory
{
    public static function fromProductException(ProductException $e): OrderException
    {
        return match($e->errorCode()){
            ProductException::NOT_FOUND => OrderException::productNotFound($e->payload()['id'], $e),
            default => OrderException::invalidProduct($e->payload()['id'], $e)
        };
    }
}
