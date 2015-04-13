<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\DependencyInjection;

use ONGR\ApiBundle\DependencyInjection\ONGRApiExtension;
use ONGR\ApiBundle\Service\DataRequestService;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests for ONGRApiExtension.
 */
class ONGRApiExtensionTest extends AbstractElasticsearchTestCase
{
    /**
     * @var string
     *
     * Root name of the configuration.
     */
    private $root = 'ongr_api';

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getManager('not_default');
    }

    /**
     * @return ONGRApiExtension
     */
    protected function getExtension()
    {
        return new ONGRApiExtension();
    }

    /**
     * @return ContainerBuilder
     */
    private function getDIContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    /**
     * Test that config is loaded properly.
     */
    public function testGetConfig()
    {
        $config = [
            'versions' => [
                'v1' => [
                    'endpoints' => [
                        'persons' => [
                            'manager' => 'es.manager.default',
                            'document' => 'AcmeTestBundle:PersonDocument',
                        ],
                        'people' => [
                            'document' => 'AcmeTestBundle:PersonDocument',
                            'manager' => 'es.manager.default',
                            'controller' => [
                                'name' => 'CustomApi',
                                'params' => [
                                    'service' => 'test_service',
                                ],
                            ],
                        ],
                    ],
                ],
                'v2' => [
                    'endpoints' => [
                        'people_names' => [
                            'manager' => 'es.manager.default',
                            'document' => 'AcmeTestBundle:PersonDocument',
                            'include_fields' => ['name'],
                        ],
                        'people_surnames' => [
                            'manager' => 'es.manager.default',
                            'document' => 'AcmeTestBundle:PersonDocument',
                            'exclude_fields' => ['name'],
                        ],
                        'person' => [
                            'manager' => 'es.manager.default',
                            'document' => 'AcmeTestBundle:PersonDocument',
                            'controller' => [
                                'name' => 'CustomPersonApi',
                                'path' => 'person/{id}',
                                'defaults' => [
                                    'id' => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expected = [
            '.versions' => ['v1', 'v2'],
            '.v1.endpoints' => ['persons', 'people'],
            '.v1.persons.controller' => ['name' => 'default'],
            '.v1.people.controller' => [
                'name' => 'CustomApi',
                'params' => [
                    'service' => 'test_service',
                ],
                'defaults' => [],
                'requirements' => [],
                'options' => [],
            ],
            '.v2.endpoints' => ['people_names', 'people_surnames', 'person'],
            '.v2.people_names.controller' => ['name' => 'default'],
            '.v2.people_surnames.controller' => ['name' => 'default'],
            '.v1.people' => [
                'manager' => 'es.manager.default',
                'document' => 'AcmeTestBundle:PersonDocument',
                'include_fields' => [],
                'exclude_fields' => [],
                'controller' => [
                    'name' => 'CustomApi',
                    'params' => [
                        'service' => 'test_service',
                    ],
                    'defaults' => [],
                    'requirements' => [],
                    'options' => [],
                ],
            ],
            '.v2.person' => [
                'manager' => 'es.manager.default',
                'document' => 'AcmeTestBundle:PersonDocument',
                'include_fields' => [],
                'exclude_fields' => [],
                'controller' => [
                    'name' => 'CustomPersonApi',
                    'path' => '/person/{id}',
                    'defaults' => ['id' => null],
                    'params' => [],
                    'requirements' => [],
                    'options' => [],
                ],
            ],
            '.v2.people_surnames' => [
                'manager' => 'es.manager.default',
                'document' => 'AcmeTestBundle:PersonDocument',
                'include_fields' => [],
                'exclude_fields' => ['name'],
                'controller' => ['name' => 'default'],
            ],
        ];

        $extension = $this->getExtension();
        $container = $this->getDIContainer();
        $extension->load([$config], $container);

        foreach ($expected as $key => $value) {
            $parameter = $this->root . $key;
            $this->assertTrue($container->hasParameter($parameter));
            $this->assertEquals($value, $container->getParameter($parameter));
        }
    }

    /**
     * Check services are created.
     */
    public function testDataRequestService()
    {
        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('v1', 'persons')
        );
        /** @var DataRequestService $dataRequest */
        $dataRequest = $this->getContainer()->get($serviceName);
        $this->assertEquals('ONGR\ApiBundle\Service\DataRequestService', get_class($dataRequest));

        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('v1', 'people')
        );
        /** @var DataRequestService $dataRequest */
        $dataRequest = $this->getContainer()->get($serviceName);
        $this->assertEquals('ONGR\ApiBundle\Service\DataRequestService', get_class($dataRequest));

        $request = new Request();
        $request->setMethod('get');
        $request->headers->set('Content-Type', 'application/json');

        $result = $dataRequest->getResponse($request);
        $result->headers->set('Date', '');

        $response = new Response();
        $response->setContent(json_encode([]));
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Date', '');

        $this->assertEquals($response, $result);
    }
}
