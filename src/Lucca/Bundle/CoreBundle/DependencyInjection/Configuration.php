<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

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
        $treeBuilder = new TreeBuilder('lucca_core');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('url')
                    ->info('Parameter used to url of application.')
                    ->defaultValue('https://lucca.local')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('asset_version')
                    ->defaultValue('1.0')
                    ->info('Version applied on all assets sended by server. Which version of file has been deployed ?')
                ->end()
                ->scalarNode('asset_forceRefresh')
                    ->defaultFalse()
                    ->info('Flag to force refresh all assets - combined with version configuration.')
                ->end()
                ->scalarNode('google_analytics_id')
                    ->info('Configure Google Analytics id to add script and register traffic')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
