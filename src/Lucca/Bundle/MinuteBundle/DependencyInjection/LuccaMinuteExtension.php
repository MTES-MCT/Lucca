<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\MinuteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

use Lucca\Bundle\CoreBundle\DependencyInjection\Configuration;

/**
 * Class LuccaMinuteExtension
 * Loads and manages your bundle configuration
 *
 * @package Lucca\Bundle\MinuteBundle\DependencyInjection
 * @author Terence <terence@numeric-wave.tech>
 */
class LuccaMinuteExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Load configuration of this Bundle
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * Load Role Hierarchy in security applications
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('security', array());
    }
}
