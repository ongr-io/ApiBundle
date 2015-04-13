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
                            'parent' => 'persons',
                            'manager' => 'es.manager.default',
                            'controller' => [
                                'name' => 'CustomApi',
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
                    ],
                ],
            ],
        ];

        $extension = $this->getExtension();
        $container = $this->getDIContainer();
        $extension->load([$config], $container);

        $parameterKey = '.versions';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $this->assertEquals(['v1', 'v2'], $container->getParameter($this->root . $parameterKey));

        $parameterKey = '.v1.endpoints';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $this->assertEquals(['persons', 'people'], $container->getParameter($this->root . $parameterKey));

        $parameterKey = '.v1.persons.controller';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $controller = $container->getParameter($this->root . $parameterKey);
        $this->assertEquals('default', $controller['name']);

        $parameterKey = '.v1.people.controller';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $controller = $container->getParameter($this->root . $parameterKey);
        $this->assertEquals('CustomApi', $controller['name']);

        $parameterKey = '.v2.endpoints';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $this->assertEquals(['people_names', 'people_surnames'], $container->getParameter($this->root . $parameterKey));

        $parameterKey = '.v2.people_names.controller';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $controller = $container->getParameter($this->root . $parameterKey);
        $this->assertEquals('default', $controller['name']);

        $parameterKey = '.v2.people_surnames.controller';
        $this->assertTrue($container->hasParameter($this->root . $parameterKey));
        $controller = $container->getParameter($this->root . $parameterKey);
        $this->assertEquals('default', $controller['name']);
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
