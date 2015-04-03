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

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('ongr_api.versions', array_keys($config['versions']));
        foreach ($config['versions'] as $versionName => $version) {
            foreach ($version['endpoints'] as $endpointName => $endpoint) {
                if (isset($endpoint['parent'])) {
                    $endpoint = $this->appendParentConfig($endpoint, $endpoint['parent'], $version['endpoints']);
                } elseif (!isset($endpoint['manager'])) {
                    throw new InvalidConfigurationException(
                        "No manager set for endpoint '$endpointName'."
                    );
                } elseif (!isset($endpoint['document'])) {
                    throw new InvalidConfigurationException(
                        "No document set for endpoint '$endpointName'."
                    );
                }
                if (!empty($endpoint['include_fields']) && !empty($endpoint['exclude_fields'])) {
                    throw new InvalidConfigurationException(
                        "'include_fields' and 'exclude_fields' can not be used together in endpoint '$endpointName'."
                    );
                }

                if (!isset($endpoint['controller'])) {
                    $endpoint['controller'] = 'default';
                } elseif (isset($endpoint['controller']['path'])
                    && strpos($endpoint['controller']['path'], '/') !== 0
                ) {
                    $endpoint['controller']['path'] = '/' . $endpoint['controller']['path'];
                }

                // Data request services are generated only for endpoints with default controllers.
                if ($endpoint['controller'] === 'default') {
                    $this->generateDataRequestService($container, $versionName, $endpointName, $endpoint);
                }

                $container->setParameter("ongr_api.$versionName.$endpointName.controller", $endpoint['controller']);
                $container->setParameter("ongr_api.$versionName.$endpointName", $endpoint);
            }
            $container->setParameter("ongr_api.$versionName.endpoints", array_keys($version['endpoints']));
        }
    }

    /**
     * Appends settings to endpoint from parent endpoint.
     *
     * @param array  $endpoint
     * @param string $parentName
     * @param array  $endpoints
     * @param array  $children
     *
     * @return array
     * @throws InvalidConfigurationException
     */
    private function appendParentConfig($endpoint, $parentName, $endpoints, $children = [])
    {
        if (!array_key_exists($parentName, $endpoints)) {
            throw new InvalidConfigurationException(
                "Invalid parent endpoint '$parentName'."
            );
        }
        $parent = $endpoints[$parentName];
        if (in_array($parent, $children)) {
            throw new InvalidConfigurationException(
                "Endpoint '$parentName' can not be ancestor of itself."
            );
        }
        $children[] = $parent;
        if (isset($parent['parent'])) {
            $parent = $this->appendParentConfig($parent, $parent['parent'], $endpoints, $children);
        }
        $endpoint = array_merge($parent, $endpoint);

        return $endpoint;
    }

    /**
     * Generate data request services.
     *
     * @param ContainerBuilder $container
     * @param string           $versionName
     * @param string           $endpointName
     * @param array            $endpoint
     */
    private function generateDataRequestService($container, $versionName, $endpointName, $endpoint)
    {
        $fields = [];
        $fields['exclude_fields'] = $endpoint['exclude_fields'];
        $fields['include_fields'] = $endpoint['include_fields'];

        $definition = new Definition(
            $container->getParameter(
                'ongr_api.data_request.class'
            ),
            [
                new Reference('service_container'),
                $endpoint['manager'],
                $endpoint['document'],
                $fields,
            ]
        );

        $container->setDefinition(
            self::getServiceNameWithNamespace(
                'data_request',
                self::getNamespaceName($versionName, $endpointName)
            ),
            $definition
        );
    }

    /**
     * Gets namespace string according to configuration version and endpoint given.
     *
     * @param string $version
     * @param string $endpoint
     *
     * @return string
     */
    public static function getNamespaceName($version, $endpoint)
    {
        $namespace = 'ongr_api.service.' . $version . '.' . $endpoint . '.%s';

        return $namespace;
    }

    /**
     * Gets service full name, by it's namespace and short name.
     *
     * @param string $name
     * @param string $namespace
     *
     * @return string
     */
    public static function getServiceNameWithNamespace($name, $namespace)
    {
        return sprintf($namespace, $name);
    }
}
