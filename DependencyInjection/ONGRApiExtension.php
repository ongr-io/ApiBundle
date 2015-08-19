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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads and manages your bundle configuration.
 */
class ONGRApiExtension extends Extension
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * @var string
     */
    private $version = '';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $collection = new Definition('Symfony\Component\Routing\RouteCollection');

        foreach ($config['versions'] as $versionName => $version) {
            $this->collectRoutes($versionName, $version['endpoints'], $collection);
        }

        $collection->setPublic(false);
        $container->setDefinition('ongr_api.route_collection', $collection);
        $container->setParameter('ongr_api.default_encoding', $config['default_encoding']);
    }

    /**
     * Populates endpoints into route collection
     *
     * @param array      $endpoints
     * @param string     $version
     * @param Definition $collectionDefinition
     */
    private function collectRoutes($version, array $endpoints, $collectionDefinition)
    {
        $this
            ->setEndpointConfig($endpoints)
            ->setVersion($version);

        foreach ($this->generate() as $name => $config) {
            $route = new Definition('Symfony\Component\Routing\Route', $config);
            $collectionDefinition->addMethodCall('add', [$name, $route]);
        }
    }

    /**
     * Generates configuration for each route.
     *
     * @return \Generator
     */
    public function generate()
    {
        foreach ($this->getEndpointConfig() as $name => $config) {
            foreach ($config['documents'] as $docConfig) {
                list(,$type) = explode(':', $docConfig['name'], 2);
                $c = [
                    'url' => $this->formatUrl($name, $type),
                    'defaults' => [
                        'id' => null,
                        'manager' => $config['manager'],
                        'repository' => $docConfig['name'],
                    ]
                ];

                foreach ($docConfig['methods'] as $method) {
                    $c['defaults']['_controller'] = $docConfig['controller'] . ':' . strtolower($method);
                    $c['requirements']['_method'] = $method;

                    yield $this->formatName($name, $type, $method) => $c;
                }
            }
        }
    }

    /**
     * Formats url for endpoint
     *
     * @param string $endpoint
     * @param string $type
     *
     * @return string
     */
    protected function formatUrl($endpoint, $type)
    {
        return sprintf(
            "%s%s%s/{id}",
            $this->getVersion() . '/',
            $endpoint === 'default' ? '' : strtolower($endpoint) . '/',
            strtolower($type)
        );
    }

    /**
     * Formats route name.
     *
     * @param string $endpoint
     * @param string $type
     * @param string $method
     *
     * @return string
     */
    protected function formatName($endpoint, $type, $method)
    {
        return strtolower(sprintf('ongr_api_%s_%s_%s_%s', $this->getVersion(), $endpoint, $type, $method));
    }

    /**
     * @param array $configuration
     *
     * @return ONGRApiExtension
     */
    protected function setEndpointConfig($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @param string $version
     *
     * @return ONGRApiExtension
     */
    protected function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return array
     */
    protected function getEndpointConfig()
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    protected function getVersion()
    {
        return $this->version;
    }
}
