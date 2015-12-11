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
            if ($config['version_in_url']) {
                $path .= '/'.$version;
            }

            foreach ($endpoints as $name => $endpoint) {
                $routeConfig = [
                    'path' => strtolower(sprintf('%s/%s/{id}', $path, $endpoint)),
                    'defaults' => [
                        'id' => null,
                        'repository' => $endpoint['repository'],
                        '_endpoint' => $endpoint,
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
}
