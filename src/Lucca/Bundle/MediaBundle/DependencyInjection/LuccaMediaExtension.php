<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\MediaBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\{ContainerBuilder, Loader};
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class LuccaMediaExtension extends Extension
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
        $container->setParameter('lucca_media', $config);

        /** Path parameters to register */
        foreach ($config as $parameter => $value) {
            $container->setParameter(sprintf('lucca_media.%s', $parameter), $value);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        /** Get Twig resources registered to form display */
        $twigResources = $container->getParameter('twig.form.resources');
        /** Merge specific config with Twig resources */
        $container->setParameter('twig.form.resources', array_merge(["@LuccaMedia/Media/Theme/widget-media.html.twig"], $twigResources));
    }

    /**
     * Load Role Hierarchy in security applications
     */
    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('security', array(
            'role_hierarchy' => array(
                // Entity - Media
                'ROLE_MEDIA_READ' => 'ROLE_USER',
                'ROLE_MEDIA_WRITE' => 'ROLE_MEDIA_READ',
                'ROLE_MEDIA_TOTAL' => 'ROLE_MEDIA_WRITE',
            ),
        ));
    }
}
