<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * afero general public license (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\DecisionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\DependencyInjection\Loader,
    Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension;

use Lucca\Bundle\CoreBundle\DependencyInjection\Configuration;

use Exception;

class LuccaMinuteExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Load configuration of this Bundle
     *
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Load Role Hierarchy in security applications
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('security', array());
    }
}

