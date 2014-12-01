<?php

namespace Success\RelationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('success_relation');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
              ->scalarNode('class')->isRequired()->end()
              ->scalarNode('manager')->defaultValue('Success\RelationBundle\Manager\RelationManager')->end()
            ->end()
        ;
        
        /*$rootNode
            ->addDefaultsIfNotSet()
            ->children()
              ->arrayNode('paypal')
                  ->addDefaultsIfNotSet()
                  ->children()
                    ->scalarNode('class')->isRequired()->end()
                  ->end()
              ->end()
            ->end()
        ;*/

        return $treeBuilder;
    }
}
