<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\Routing;

use ONGR\ApiBundle\Routing\ElasticsearchLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouteCollection;

class ElasticsearchLoaderTest extends WebTestCase
{
    /**
     * Data provider for testing route loader.
     *
     * @return array
     */
    public function getTestLoadData()
    {
        return [
            [
                'ongr_api_v1_default_person_get',
                '/v1/person/{id}',
                'GET',
                [
                    'id' => null,
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:getAction',
                    '_allow_extra_fields' => true,
                ],
            ],
            [
                'ongr_api_v1_default_person_post',
                '/v1/person/{id}',
                'POST',
                [
                    'id' => null,
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:postAction',
                    '_allow_extra_fields' => true,
                ],
            ],
            [
                'ongr_api_v1_custom_person_post',
                '/v1/custom/person/{id}',
                'POST',
                [
                    'id' => null,
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:postAction',
                    '_allow_extra_fields' => false,
                ],
            ],
            [
                'ongr_api_v1_custom_person_get',
                '/v1/custom/person/{id}',
                'GET',
                [
                    'id' => null,
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:getAction',
                    '_allow_extra_fields' => false,
                ],
            ],
            [
                'ongr_api_v1_custom_person_put',
                '/v1/custom/person/{id}',
                'PUT',
                [
                    'id' => null,
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:putAction',
                    '_allow_extra_fields' => false,
                ],
            ],
            [
                'ongr_api_v1_custom_person_delete',
                '/v1/custom/person/{id}',
                'DELETE',
                [
                    'id' => null,
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:deleteAction',
                    '_allow_extra_fields' => false,
                ],
            ],
            [
                'ongr_api_v2_default_person_post',
                '/v2/person/{id}',
                'POST',
                [
                    'id' => null,
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v2',
                    '_controller' => 'ongr_api.rest_controller:postAction',
                    '_allow_extra_fields' => true,
                ],
            ],
            [
                'ongr_api_v2_default_person_get',
                '/v2/person/{id}',
                'GET',
                [
                    'id' => null,
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v2',
                    '_controller' => 'ongr_api.rest_controller:getAction',
                    '_allow_extra_fields' => true,
                ],
            ],
            [
                'ongr_api_v2_default_person_put',
                '/v2/person/{id}',
                'PUT',
                [
                    'id' => null,
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v2',
                    '_controller' => 'ongr_api.rest_controller:putAction',
                    '_allow_extra_fields' => true,
                ],
            ],
            [
                'ongr_api_v2_default_person_delete',
                '/v2/person/{id}',
                'DELETE',
                [
                    'id' => null,
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_type' => 'person',
                    '_version' => 'v2',
                    '_controller' => 'ongr_api.rest_controller:deleteAction',
                    '_allow_extra_fields' => true,
                ],
            ],
            [
                'ongr_api_v2_batch',
                '/v2/batch',
                'POST',
                [
                    '_version' => 'v2',
                    '_controller' => 'ongr_api.batch_controller:batchAction',
                ],
            ],
            [
                'ongr_api_command_v1_default_index_create',
                '/v1/_command/index/create',
                'POST',
                [
                    '_controller' => 'ongr_api.command_controller:createIndexAction',
                    '_version' => 'v1',
                    'manager' => 'es.manager.default',
                ],
            ],
            [
                'ongr_api_command_v1_default_index_drop',
                '/v1/_command/index/drop',
                'POST',
                [
                    '_controller' => 'ongr_api.command_controller:dropIndexAction',
                    '_version' => 'v1',
                    'manager' => 'es.manager.default',
                ],
            ],
            [
                'ongr_api_command_v1_default_schema_update',
                '/v1/_command/schema/update',
                'POST',
                [
                    '_controller' => 'ongr_api.command_controller:updateSchemaAction',
                    '_version' => 'v1',
                    'manager' => 'es.manager.default',
                ],
            ],
        ];
    }

    /**
     * Tests loaded paths.
     *
     * @param string $name
     * @param string $path
     * @param string $method
     * @param array  $defaults
     *
     * @dataProvider getTestLoadData()
     */
    public function testLoad($name, $path, $method, $defaults)
    {
        /** @var RouteCollection $collection */
        $collection = $this->getLoader()->load('');

        $this->assertEquals(14, $collection->count(), 'Loaded route number has changed!');
        $route = $collection->get($name);

        $this->assertNotNull($route, 'Route cannot be null');
        $this->assertEquals($path, $route->getPath(), 'Route path does not match');
        $this->assertEquals([$method], $route->getMethods(), 'Route has wrong method');
        $this->assertEquals($defaults, $route->getDefaults(), 'Route default params does not match');
    }

    /**
     * @return ElasticsearchLoader
     */
    private function getLoader()
    {
        return static::createClient()
            ->getContainer()
            ->get('ongr_api.elasticsearch_loader');
    }
}
