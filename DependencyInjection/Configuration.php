<?php

namespace Reactorcoder\Symfony2NodesocketBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('reactorcoder_symfony2_nodesocket');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode->children()
                ->scalarNode('host')->defaultValue('')->end()
                ->scalarNode('port')->defaultValue('')->end()
                ->scalarNode('origin')->defaultValue(array(''))->end()
                ->arrayNode('allowedServers')
                    ->canBeUnset()->prototype('scalar')->end()->end()
                ->scalarNode('dbOptions')->defaultValue('')->end()
                ->scalarNode('checkClientOrigin')->defaultValue('')->end()
                ->scalarNode('sessionVarName')->defaultValue('')->end()
                ->scalarNode('socketLogFile')->defaultValue('')->end()
                ->scalarNode('pidFile')->defaultValue('')->end()
                ->scalarNode('gritter')->defaultValue('')->end()
                //->booleanNode('cookie')->defaultTrue()->end()
            ->end()
            ;
        
        return $treeBuilder;
    }
}
