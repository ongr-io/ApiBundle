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
 * Loads and manages your bundle configuration.
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
            if (isset($version['parent'])) {
                $version = $this->appendParentConfig($version, $version['parent'], $config['versions']);
            } elseif (empty($version['endpoints'])) {
                throw new InvalidConfigurationException(
                    "At least one endpoint must be configured in version '$versionName'."
                );
            }
            foreach ($version['endpoints'] as $endpointName => $endpoint) {
                if (!isset($endpoint['controller'])) {
                    $endpoint['controller'] = ['name' => 'default'];
                    if (!isset($endpoint['manager'])) {
                        throw new InvalidConfigurationException(
                            "Manager must be set, when using default controller. (Endpoint: '$endpointName')"
                        );
                    }
                    if (!isset($endpoint['document'])) {
                        throw new InvalidConfigurationException(
                            "Document must be set, when using default controller. (Endpoint: '$endpointName')"
                        );
                    }
                }
                if (!empty($endpoint['include_fields']) && !empty($endpoint['exclude_fields'])) {
                    throw new InvalidConfigurationException(
                        "'include_fields' and 'exclude_fields' can not be used together in endpoint '$endpointName'."
                    );
                }

                if (isset($endpoint['controller']['path'])
                    && strpos($endpoint['controller']['path'], '/') !== 0
                ) {
                    $endpoint['controller']['path'] = '/' . $endpoint['controller']['path'];
                }

                // Data request services are generated only for endpoints with default controllers.
                if ($endpoint['controller']['name'] === 'default') {
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
     * @param array  $node
     * @param string $parentName
     * @param array  $nodeset
     * @param array  $children
     *
     * @return array
     * @throws InvalidConfigurationException
     */
    private function appendParentConfig($node, $parentName, $nodeset, $children = [])
    {
        if (!array_key_exists($parentName, $nodeset)) {
            throw new InvalidConfigurationException(
                "Invalid parent '$parentName'."
            );
        }
        $parent = $nodeset[$parentName];
        if (in_array($parentName, $children)) {
            throw new InvalidConfigurationException(
                "'$parentName' can not be ancestor of itself."
            );
        }

        $children[] = $parentName;
        if (isset($parent['parent'])) {
            $parent = $this->appendParentConfig($parent, $parent['parent'], $nodeset, $children);
        }

        $node['endpoints'] = array_merge($parent['endpoints'], $node['endpoints']);

        return $node;
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
                $endpointName,
                new Reference('event_dispatcher'),
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
