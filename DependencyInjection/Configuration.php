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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges configuration from your app/config files.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ongr_api');

        $rootNode
            ->children()
                ->arrayNode('versions')
                    ->info('Defines api versions.')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('version')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('version')->end()
                            ->arrayNode('endpoints')
                                ->info('Defines version endpoints.')
                                ->useAttributeAsKey('endpoint')

                                ->prototype('array')
                                    ->info('Holds endpoint config.')
                                    ->children()
                                        ->scalarNode('endpoint')->end()
                                        ->scalarNode('manager')
                                            ->info('Elastic search manager name.')
                                            ->example('es.manager.default')
                                        ->end()
                                        ->scalarNode('document')
                                            ->info('Logical document class name.')
                                            ->example('ONGRDemoBundle:ProductDocument')
                                        ->end()
                                        ->arrayNode('include_fields')
                                            ->prototype('scalar')->end()
                                            ->info(
                                                'Document fields to include. Can not be used with \'exclude_fields\'.'
                                            )
                                            ->example(['title', 'price'])
                                        ->end()
                                        ->arrayNode('exclude_fields')
                                            ->prototype('scalar')->end()
                                            ->info(
                                                'Document fields to exclude. Can not be used with \'include_fields\'.'
                                            )
                                            ->example(['id', 'slug'])
                                        ->end()
                                        ->arrayNode('controller')
                                            ->info('Defines controller to override default one.')
                                            ->children()
                                                ->scalarNode('name')
                                                    ->isRequired()
                                                    ->info('Logical controller name.')
                                                    ->example('ONGRDemoBundle:CustomApi')
                                                ->end()
                                                ->scalarNode('path')
                                                    ->info('Routing path, where endpoint will be available.')
                                                    ->example('/{id}/{field}')
                                                ->end()
                                                ->arrayNode('defaults')
                                                    ->prototype('variable')->end()
                                                    ->info('Route parameters defaults.')
                                                    ->example(
                                                        [
                                                            'id' => 1,
                                                            'field' => 'title',
                                                        ]
                                                    )
                                                ->end()
                                                ->arrayNode('requirements')
                                                    ->info('Route requirements.')
                                                    ->prototype('variable')->end()
                                                ->end()
                                                ->arrayNode('options')
                                                    ->info('Route options.')
                                                    ->prototype('variable')->end()
                                                ->end()
                                                ->arrayNode('params')
                                                    ->info('Anything you want to pass to controller as parameters.')
                                                    ->example(
                                                        [
                                                            'simple_param' => 123,
                                                            'array_param' => [
                                                                'some_string' => 'string',
                                                                'some_int' => 321,
                                                            ],
                                                        ]
                                                    )
                                                    ->prototype('variable')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('parent')
                                ->info('Defines version from which configuration will be inherited.')
                                ->example('v1')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
