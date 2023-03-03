<?php

declare(strict_types=1);

namespace Owl\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterResourceFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('owl.registry.resource_filter')) {
            return;
        }

        $registry = $container->getDefinition('owl.registry.resource_filter');

        foreach ($container->findTaggedServiceIds('owl.resource_filter') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                $registry->addMethodCall('register', [$attribute['type'], new Reference($id)]);
            }
        }
    }
}
