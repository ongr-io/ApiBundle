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
use Symfony\Component\DependencyInjection\Container;
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

        $container->setParameter('ongr_api.output_format', $config['output_format']);
        $container->setParameter('ongr_api.version_in_url', $config['version_in_url']);
        $container->setParameter('ongr_api.versions', $config['versions']);

        $collection = new Definition('Symfony\Component\Routing\RouteCollection');

        foreach ($config['versions'] as $version => $endpoints) {
            $path = '';
            if ($config['output_format']) {
                $path .= '/'.$version;
            }

            foreach ($endpoints as $name => $endpoint) {
                $config = [
                    'url' => $this->formatUrl($name, $type),
                    'defaults' => [
                        'id' => null,
                        'repository' => $endpoint['repository'],
                        '_type' => strtolower($type),
                        '_version' => $this->getVersion(),
                        '_allow_extra_fields' => $endpoint['allow_extra_fields'],
                    ],
                ];

                $route = new Definition('Symfony\Component\Routing\Route', $routeConfig);
                $collection->addMethodCall('add', [$name, $route]);
            }
        }
    }

    /**
     * If authorization is enabled authentication listener is registered.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    private function registerAuthenticationListener(array $config, ContainerBuilder $container)
    {
        if ($config['authorization']['enabled']) {
            $definition = new Definition(
                $container->getParameter('ongr_api.event_listener.authentication.class'),
                [
                    new Reference('service_container'),
                    $config['authorization']['secret'],
                ]
            );
            $definition->setTags(
                [
                    'kernel.event_listener' => [
                        ['event' => 'kernel.request', 'method' => 'onKernelRequest', 'priority' => 10],
                    ],
                ]
            );

            $container->setDefinition('ongr_api.event_listener.authentication', $definition);
        }
    }

    /**
     * Populates endpoints into route collection.
     *
     * @param array            $config
     * @param ContainerBuilder $builder
     */
    private function collectRoutes(array $config, ContainerBuilder $builder)
    {
        $collection = new Definition('Symfony\Component\Routing\RouteCollection');

        foreach ($config as $version => $endpoints) {
            $this
                ->setEndpointConfig($endpoints['endpoints'])
                ->setVersion($version);

            foreach ($this->generate() as $name => $routeConfig) {
                $route = new Definition('Symfony\Component\Routing\Route', $routeConfig);
                $collection->addMethodCall('add', [$name, $route]);
            }

            if ($endpoints['batch']['enabled']) {
                $route = $this->getBatchRoute($endpoints['batch']);
                $collection->addMethodCall('add', [sprintf('ongr_api_%s_batch', $this->getVersion()), $route]);
            }
        }

        $collection->setPublic(false);
        $builder->setDefinition('ongr_api.route_collection', $collection);
    }

    /**
     * Generates configuration for each route.
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
                        '_type' => strtolower($type),
                        '_version' => $this->getVersion(),
                        '_allow_extra_fields' => $docConfig['allow_extra_fields'],
                    ],
                ];

                foreach ($docConfig['methods'] as $method) {
                    $c['defaults']['_controller'] = $docConfig['controller'] . ':' . strtolower($method) . 'Action';
                    $c['requirements']['_method'] = $method;

                    yield $this->formatName($name, $type, $method) => $c;
                }
            }

            if ($config['commands']['enabled']) {
                foreach ($config['commands']['commands'] as $cmd) {
                    list($command, $action) = explode(':', $cmd, 2);
                    yield $this->formatCommandName($name, $command, $action) => [
                        'url' => $this->formatCommandUrl($name, $command, $action),
                        'defaults' => [
                            '_controller' => $config['commands']['controller'] . ':'
                                . strtolower($action) . ucfirst($command) . 'Action',
                            '_version' => $this->getVersion(),
                            'manager' => $config['manager'],
                        ],
                        'requirements' => [
                            '_method' => 'POST',
                        ],
                    ];
                }
            }
        }
    }

    /**
     * Formats url for endpoint.
     *
     * @param string $endpoint
     * @param string $type
     *
     * @return string
     */
    private function formatUrl($endpoint, $type)
    {
        return strtolower(
            sprintf(
                '%s/%s%s/{id}',
                $this->getVersion(),
                $endpoint === 'default' ? '' : $endpoint . '/',
                $type
            )
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
    private function formatName($endpoint, $type, $method)
    {
        return strtolower(sprintf('ongr_api_%s_%s_%s_%s', $this->getVersion(), $endpoint, $type, $method));
    }

    /**
     * Formats command route name.
     *
     * @param string $endpoint
     * @param string $command
     * @param string $action
     *
     * @return string
     */
    private function formatCommandName($endpoint, $command, $action)
    {
        return strtolower(
            sprintf(
                'ongr_api_command_%s_%s_%s_%s',
                $this->getVersion(),
                $endpoint,
                $command,
                $action
            )
        );
    }

    /**
     * Builds batch route definition.
     *
     * @param array $config
     *
     * @return Definition
     */
    private function getBatchRoute(array $config)
    {
        return new Definition(
            'Symfony\Component\Routing\Route',
            [
                'url' => $this->getVersion() . '/batch',
                'defaults' => [
                    '_version' => $this->getVersion(),
                    '_controller' => $config['controller'] . ':batchAction',
                ],
                'requirements' => [
                    '_method' => 'POST',
                ],
            ]
        );
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
