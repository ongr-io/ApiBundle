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
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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
                if (isset($endpoint['parent'])) {
                    $endpoint = $this->appendParentConfig($endpoint, $endpoint['parent'], $version['endpoints']);
                } elseif ($endpoint['manager'] === null) {
                    throw new InvalidConfigurationException(
                        "No manager set for endpoint '$endpointName'."
                    );
                }
                $container->setParameter("ongr_api.$versionName.$endpointName.manager", $endpoint['manager']);
                $container->setParameter("ongr_api.$versionName.$endpointName.documents", $endpoint['documents']);
                $container->setParameter("ongr_api.$versionName.$endpointName.controller", $endpoint['controller']);
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Appends documents to endpoint from parent endpoint.
     *
     * @param array  $endpoint
     * @param string $parentName
     * @param array  $endpoints
     *
     * @return array
     * @throws InvalidConfigurationException
     */
    private function appendParentConfig($endpoint, $parentName, $endpoints)
    {
        if (!array_key_exists($parentName, $endpoints)) {
            throw new InvalidConfigurationException(
                "Invalid parent endpoint '$parentName'."
            );
        }
        $parent = $endpoints[$parentName];
        if (isset($parent['parent'])) {
            $parent = $this->appendParentConfig($parent, $parent['parent'], $endpoints);
        }
        $endpoint['documents'] = array_unique(array_merge($endpoint['documents'], $parent['documents']));
        if ($endpoint['manager'] === null) {
            $endpoint['manager'] = $parent['manager'];
        }

        return $endpoint;
    }
}
