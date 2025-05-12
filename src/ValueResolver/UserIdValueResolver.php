<?php

namespace App\ValueResolver;

use App\Doctrine\Entity\IdentifierInterface;
use Symfony\Component\HttpKernel\Attribute\AsTargetedValueResolver;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

#[AsTargetedValueResolver('user_id')]
class UserIdValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // get the argument type (e.g. UserId)
        $argumentType = $argument->getType();
        if (
            !$argumentType
            || !is_subclass_of($argumentType, IdentifierInterface::class, true)
        ) {
            return [];
        }

        // get the value from the request, based on the argument name
        $value = $request->attributes->get($argument->getName());
        if (!is_string($value)) {
            return [];
        }

        // create and return the value object
        return [$argumentType::fromString($value)];
    }
}
