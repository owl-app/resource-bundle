<?php

declare(strict_types=1);

namespace Owl\Bundle\ResourceBundle\DependencyInjection\Compiler;

use Sylius\Component\Resource\Metadata\Metadata;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

final class ActionsResourcePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $resourceRegistry = $container->get('sylius.resource_registry');
 
        foreach ($container->findTaggedServiceIds('owl.controller.action') as $id => $attributes) {
            $alias = null;

            foreach ($attributes as $attribute) {
                if (isset($attribute['alias'])) {
                    $alias = $attribute['alias'];
                }
            }

            if(is_null($alias)) {
                throw new InvalidArgumentException('Tagged resource actions needs to have "alias" attribute.');
            }

            $definition = $container->findDefinition($id);
            $metadata = $resourceRegistry->get($alias);
 
            $definition->addMethodCall('setMetadata', [$this->getMetadataDefinition($metadata)]);
            $definition->addMethodCall('setRepository', [new Reference($metadata->getServiceId('repository'))]);
            $definition->addMethodCall('setFactory', [new Reference($metadata->getServiceId('factory'))]);
            $definition->addMethodCall('setManager', [new Reference($metadata->getServiceId('manager'))]);
            $definition->addMethodCall('setAuthorizationChecker', [
                new Reference('sylius.resource_controller.authorization_checker')
            ]);
            $definition->addMethodCall('setEventDispatcher', [
                new Reference('sylius.resource_controller.event_dispatcher')
            ]);
            $definition->addMethodCall('setContainer', [new Reference('service_container')]);
        }

    }

    protected function getMetadataDefinition(MetadataInterface $metadata): Definition
    { 
        $definition = new Definition(Metadata::class);
        $definition
            ->setFactory([new Reference('sylius.resource_registry'), 'get'])
            ->setArguments([$metadata->getAlias()])
        ;

        return $definition;
    }
}
