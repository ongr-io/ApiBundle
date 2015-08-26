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
                    'type' => 'person',
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:getAction',
                ],
            ],
            [
                'ongr_api_v1_custom_person_post',
                '/v1/custom/person/{id}',
                'POST',
                [
                    'id' => null,
                    'type' => 'person',
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:postAction',
                ],
            ],
            [
                'ongr_api_v1_custom_person_get',
                '/v1/custom/person/{id}',
                'GET',
                [
                    'id' => null,
                    'type' => 'person',
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:getAction',
                ],
            ],
            [
                'ongr_api_v1_custom_person_put',
                '/v1/custom/person/{id}',
                'PUT',
                [
                    'id' => null,
                    'type' => 'person',
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:putAction',
                ],
            ],
            [
                'ongr_api_v1_custom_person_delete',
                '/v1/custom/person/{id}',
                'DELETE',
                [
                    'id' => null,
                    'type' => 'person',
                    'manager' => 'es.manager.not_default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_version' => 'v1',
                    '_controller' => 'ongr_api.rest_controller:deleteAction',
                ],
            ],
            [
                'ongr_api_v2_default_person_post',
                '/v2/person/{id}',
                'POST',
                [
                    'id' => null,
                    'type' => 'person',
                    'manager' => 'es.manager.default',
                    'repository' => 'AcmeTestBundle:Person',
                    '_version' => 'v2',
                    '_controller' => 'ongr_api_test.rest_controller:postAction',
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

        $this->assertEquals(7, $collection->count());
        $route = $collection->get($name);

        $this->assertNotNull($route);
        $this->assertEquals($path, $route->getPath());
        $this->assertEquals([$method], $route->getMethods());
        $this->assertEquals($defaults, $route->getDefaults());
    }

    /**
     * Tests if exception is thown when loading more than once.
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Do not add the "ongr_api" loader twice
     */
    public function testLoadException()
    {
        $loader = $this->getLoader();
        $loader->load('');
        $loader->load('');
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
