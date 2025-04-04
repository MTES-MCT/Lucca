<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SecurityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface,
    Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition,
    Symfony\Component\Config\Definition\Builder\NodeDefinition;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Create a tree configuration for each parameter defined
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lucca_security');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->enumNode('type')
                    ->isRequired()
                    ->values(['form', 'cas', 'google'])
                ->end()
                ->scalarNode('default_url_after_login')
                    ->isRequired()
                    ->info('This is the default url called after a success login attempt.')
                ->end()
                ->scalarNode('authentication_api_key')
                    ->info('Key used to authenticate user on this main API.')
                ->end()
            ->end();

        /** Add basic nodes for protection section */
        $rootNode->append($this->addNodesProtection());

        return $treeBuilder;
    }

    /**
     * Create subtree to define protection nodes
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    public function addNodesProtection(): ArrayNodeDefinition|NodeDefinition
    {
        $treeBuilder = new TreeBuilder('protection');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->integerNode('max_login_attempts')
                    ->min(0)->max(50)
                    ->defaultValue(5)
                    ->info('This value is used to block login attempts.')
                ->end()
                ->integerNode('period_max_in_min')
                    ->min(0)->max(9999)
                    ->defaultValue(30)
                    ->info('Scan every login attempts in last xx minutes. How many ?')
                ->end()
                ->integerNode('period_clean_in_day')
                    ->min(0)->max(999)
                    ->defaultValue(3)
                    ->info('Scan every login attempts in last xx days to unban them. How many ?')
                ->end()
            ->end();

        return $node;
    }
}
