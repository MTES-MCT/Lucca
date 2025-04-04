<?php

/*
 * copyright (c) 2025. numeric wave
 *
 * Affero General Public License (agpl) v3
 *
 * for more information, please refer to the license file at the root of the project.
 */

namespace Lucca\Bundle\FolderBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class LuccaFolderExtension extends Extension implements PrependExtensionInterface
{
    /**
     * Load configuration of this Bundle
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('lucca_folder', $config);

        /** Path parameters to register */
        foreach ($config as $parameter => $value) {
            $container->setParameter(sprintf('lucca_folder.%s', $parameter), $value);
        }

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
