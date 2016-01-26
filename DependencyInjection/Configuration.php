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

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Request;

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
                    ->useAttributeAsKey('version')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('versions')
                            ->info('Defines a version for current api endpoints.')
                            ->example('v1')
                            ->end()
                        ->append($this->getEndpointNode())
                        ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * Builds configuration tree for endpoints.
     *
     * @return NodeDefinition
     */
    public function getEndpointNode()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('endpoints');

        $node
            ->info('Defines version endpoints.')
            ->useAttributeAsKey('endpoint')
            ->prototype('array')
                ->children()

                    ->scalarNode('endpoint')
                        ->info('Endpoint name (will be included in url (e.g. products))')
                        ->example('products')
                    ->end()

                    ->scalarNode('repository')
                        ->isRequired()
                        ->info('Document service from Elasticsearch bundle which will be used for data fetching')
                        ->example('es.manager.default.products')
                    ->end()

                    ->arrayNode('methods')
                        ->defaultValue(
                            [
                                Request::METHOD_POST,
                                Request::METHOD_GET,
                                Request::METHOD_PUT,
                                Request::METHOD_DELETE
                            ]
                        )
                        ->prototype('scalar')
                        ->validate()
                            ->ifNotInArray(
                                [
                                    Request::METHOD_HEAD,
                                    Request::METHOD_POST,
                                    Request::METHOD_PATCH,
                                    Request::METHOD_GET,
                                    Request::METHOD_PUT,
                                    Request::METHOD_DELETE
                                ]
                            )
                            ->thenInvalid(
                                'Invalid HTTP method used! Please check your ongr_api endpoint configuration.'
                            )
                            ->end()
                        ->end()
                    ->end()

                    ->booleanNode('allow_extra_fields')
                        ->defaultFalse()
                        ->info(
                            'Allows to pass unknown fields to an api. '.
                            'Make sure you have configured elasticsearch respectively.'
                        )
                    ->end()

                    ->arrayNode('allow_fields')
                        ->defaultValue([])
                        ->info('A list off a allowed fields to operate through api for a document.')
                        ->prototype('scalar')->end()
                    ->end()

                    ->booleanNode('allow_get_all')
                    ->defaultTrue()
                    ->info(
                        'Allows to use `_all` elasticsearch api to get all documents from a type.'
                    )
                    ->end()

                    ->booleanNode('allow_batch')
                    ->defaultTrue()
                    ->info(
                        'Allows to use `_batch` elasticsearch api to pass multiple documents in single API request.'
                    )
                    ->end()

                    ->booleanNode('variants')
                    ->defaultFalse()
                    ->info(
                        'If set to true user can manipulate document variants over API.'
                    )
                    ->end()

                ->end()
            ->end();

        return $node;
    }
}
