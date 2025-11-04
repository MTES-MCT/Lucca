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
                ->scalarNode('asset_version')
                    ->defaultValue('1.0')
                    ->info('Version applied on all assets sended by server. Which version of file has been deployed ?')
                ->end()
                ->scalarNode('lucca_unit_test_dep_code')
                    ->info('Parameter used to define the department code for unit tests.')
                    ->defaultValue(null)
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('asset_forceRefresh')
                    ->defaultFalse()
                    ->info('Flag to force refresh all assets - combined with version configuration.')
                ->end()
                ->scalarNode('google_analytics_id')
                    ->info('Configure Google Analytics id to add script and register traffic')
                ->end()
                ->scalarNode('aigle_api_base_url')
                    ->info('Configure Aigle API base url to connect to Aigle services')
                ->end()
                ->scalarNode('aigle_api_key')
                    ->info('Configure Aigle API key to connect to Aigle services')
                ->end()
                ->scalarNode('aigle_api_key')
                    ->info('Configure Aigle API key to connect to Aigle services')
                ->end()
                ->scalarNode('lucca_rest_api_key')
                    ->defaultValue('')
                    ->info('API key used to secure REST API calls.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
