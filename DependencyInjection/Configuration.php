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
 * This is the class that validates and merges configuration from your app/config files.
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
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->example(
                        [
                            'v1' => [
                                'endpoints' => [
                                    'endpoint' => ['manager' => 'default', 'documents' => ['ONGRDemoBundle:ProductDocument']]
                                ],
                            ],
                        ]
                    )
                    ->useAttributeAsKey('version')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('version')->end()
                            ->arrayNode('endpoints')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->useAttributeAsKey('endpoint')

                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('endpoint')->end()
                                        ->scalarNode('manager')
                                            ->isRequired()
                                        ->end()
                                        ->arrayNode('documents')
                                            ->isRequired()
                                            ->requiresAtLeastOneElement()
                                            ->prototype('scalar')->end()
                                        ->end()
                                        ->scalarNode('controller')
                                            ->defaultValue('default') //DEFAULT CONTROLLER
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
