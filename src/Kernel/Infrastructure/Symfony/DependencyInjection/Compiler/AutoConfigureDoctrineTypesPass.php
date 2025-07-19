<?php declare(strict_types=1);


namespace Kernel\Infrastructure\Symfony\DependencyInjection\Compiler;

use Kernel\Infrastructure\Persistence\Doctrine\Types\DoctrineCustomTypeInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class AutoConfigureDoctrineTypesPass implements CompilerPassInterface
{
    private const string DOCTRINE_TYPES_PARAM = 'doctrine.dbal.connection_factory.types';
    private const string CUSTOM_TYPE_TAG = 'doctrine.custom_type';

    public function process(ContainerBuilder $container): void
    {
        $existingTypes = $container->hasParameter(self::DOCTRINE_TYPES_PARAM)
            ? $container->getParameter(self::DOCTRINE_TYPES_PARAM)
            : [];

        // Trouve tous les services qui ont notre tag personnalisé
        $discoveredTypes = $this->discoverTypes($container);

        if (empty($discoveredTypes)) {
            return;
        }

        $container->setParameter(
            self::DOCTRINE_TYPES_PARAM,
            array_merge($existingTypes, $discoveredTypes)
        );
    }

    private function discoverTypes(ContainerBuilder $container): array {

        $discoveredTypes = [];
        $taggedServiceIds = $container->findTaggedServiceIds(self::CUSTOM_TYPE_TAG);

        foreach ($taggedServiceIds as $serviceId => $tags) {

            $typeClass = $container->getDefinition((string) $serviceId)->getClass();
            $reflection = new \ReflectionClass($typeClass);

            if (!$reflection->implementsInterface(DoctrineCustomTypeInterface::class)) {
                throw new \LogicException(sprintf('Service "%s" tagged with "%s" must implement "%s".', $serviceId, self::CUSTOM_TYPE_TAG, DoctrineCustomTypeInterface::class));
            }

            if (!$reflection->hasConstant('NAME')) {
                throw new \LogicException(sprintf('Class "%s" tagged as a doctrine custom type must have a "NAME" constant.', $typeClass));
            }

            $typeName = $reflection->getConstant('NAME');

            $discoveredTypes[$typeName] = ['class' => $typeClass];
        }

        return $discoveredTypes;
    }
}
