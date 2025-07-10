<?php

declare(strict_types=1);

namespace Kernel\Infrastructure\Persistence\Doctrine\Types;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('doctrine.custom_type')]
interface DoctrineCustomTypeInterface
{

}
