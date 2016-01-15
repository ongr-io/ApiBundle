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
        $productEndpoint = [
            'repository' => 'es.manager.default.person',
            'allow_extra_fields' => false,
            'methods' => ["GET", "POST", "PUT", "DELETE"],
            'allow_get_all' => true,
            'allow_batch' => true,
            'allow_fields' => [],
            'variants' => false,
        ];

        $tshirtEndpoint = $productEndpoint;
        $tshirtEndpoint['repository'] = 'es.manager.default.tshirt';
        $tshirtEndpoint['variants'] = true;

        return [
            [
                'ongr_api_v3_person_get',
                '/v3/person/{id}',
                'GET',
                [
                    'id' => null,
                    '_endpoint' => $productEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Rest:get',
                    'repository' => 'es.manager.default.person',
                ],
            ],
            [
                'ongr_api_v3_person_post',
                '/v3/person/{id}',
                'POST',
                [
                    'id' => null,
                    '_endpoint' => $productEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Rest:post',
                    'repository' => 'es.manager.default.person',
                ],
            ],
            [
                'ongr_api_v3_person_put',
                '/v3/person/{id}',
                'PUT',
                [
                    'id' => null,
                    '_endpoint' => $productEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Rest:put',
                    'repository' => 'es.manager.default.person',
                ],
            ],
            [
                'ongr_api_v3_person_delete',
                '/v3/person/{id}',
                'DELETE',
                [
                    'id' => null,
                    '_endpoint' => $productEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Rest:delete',
                    'repository' => 'es.manager.default.person',
                ],
            ],
            [
                'ongr_api_v3_person__all',
                '/v3/person/_all',
                'GET',
                [
                    '_endpoint' => $productEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Collection:all',
                    'repository' => 'es.manager.default.person',
                ],
            ],
            [
                'ongr_api_v3_person__batch',
                '/v3/person/_batch',
                'POST',
                [
                    '_endpoint' => $productEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Collection:batch',
                    'repository' => 'es.manager.default.person',
                ],
            ],
            [
                'ongr_api_v3_tshirt_get_variant',
                '/v3/tshirt/{documentId}/_variant/{id}',
                'GET',
                [
                    'id' => null,
                    '_endpoint' => $tshirtEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Variant:get',
                    'repository' => 'es.manager.default.tshirt',
                ],
            ],
            [
                'ongr_api_v3_tshirt_put_variant',
                '/v3/tshirt/{documentId}/_variant/{id}',
                'PUT',
                [
                    'id' => null,
                    '_endpoint' => $tshirtEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Variant:put',
                    'repository' => 'es.manager.default.tshirt',
                ],
            ],
            [
                'ongr_api_v3_tshirt_delete_variant',
                '/v3/tshirt/{documentId}/_variant/{id}',
                'DELETE',
                [
                    'id' => null,
                    '_endpoint' => $tshirtEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Variant:delete',
                    'repository' => 'es.manager.default.tshirt',
                ],
            ],
            [
                'ongr_api_v3_tshirt_post_variant',
                '/v3/tshirt/{documentId}/_variant/{id}',
                'POST',
                [
                    'id' => null,
                    '_endpoint' => $tshirtEndpoint,
                    '_version' => 'v3',
                    '_controller' => 'ONGRApiBundle:Variant:post',
                    'repository' => 'es.manager.default.tshirt',
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
     * @param array $defaults
     *
     * @dataProvider getTestLoadData()
     */
    public function testLoad($name, $path, $method, $defaults)
    {
        /** @var RouteCollection $collection */
        $collection = $this->getLoader()->load('');

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
