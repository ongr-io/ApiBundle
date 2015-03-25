<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 */
class ONGRApiExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('ongr_api.versions', array_keys($config['versions']));
        foreach ($config['versions'] as $versionName => $version) {
            $container->setParameter("ongr_api.$versionName.endpoints", array_keys($version['endpoints']));
            foreach ($version['endpoints'] as $endpointName => $endpoint) {
                $container->setParameter("ongr_api.$versionName.$endpointName.manager", $endpoint['manager']);
                $container->setParameter("ongr_api.$versionName.$endpointName.documents", $endpoint['documents']);
                $container->setParameter("ongr_api.$versionName.$endpointName.controller", $endpoint['controller']);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
