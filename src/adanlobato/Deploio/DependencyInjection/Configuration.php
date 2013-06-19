<?php

namespace adanlobato\Deploio\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('deploio');

        $rootNode
            ->children()
                ->scalarNode('name')->isRequired()->end()
                ->arrayNode('servers')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                    ->children()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->end()
                        ->scalarNode('user')->end()
                        ->scalarNode('pass')->end()
                        ->scalarNode('dir')->isRequired()->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}