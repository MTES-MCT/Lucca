<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\CoreBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\{ContainerBuilder, Loader};
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LuccaCoreExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('lucca_security', $config);

        /** Path parameters to register */
        foreach ($config as $parameter => $value) {
            $container->setParameter(sprintf('lucca_security.%s', $parameter), $value);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        /** Get configuration from config files */
        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        /** Put your config value in an array to be passed in twig bundle */
        $twigGlobals = array(
            'globals' => array(
                'google_analytics_id' => $config['google_analytics_id'],
            ),
        );

        /** Pass the array to twig bundle */
        $container->prependExtensionConfig('twig', $twigGlobals);
    }
}
