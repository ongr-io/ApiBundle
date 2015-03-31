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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests for ONGRApiExtension.
 */
class ONGRApiExtensionTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->getManager('not_default');
    }

    /**
     * Check services are  created.
     */
    public function testDataRequestService()
    {
        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('test', 'magic')
        );
        /** @var DataRequestService $dataRequest */
        $dataRequest = $this->getContainer()->get($serviceName);
        $this->assertEquals('ONGR\ApiBundle\Service\DataRequestService', get_class($dataRequest));

        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('test', 'black_magic')
        );
        /** @var DataRequestService $dataRequest */
        $dataRequest = $this->getContainer()->get($serviceName);
        $this->assertEquals('ONGR\ApiBundle\Service\DataRequestService', get_class($dataRequest));

        $request = new Request();
        $request->setMethod('get');
        $request->headers->set('Content-Type', 'application/json');

        $result = $dataRequest->getResponse($request);

        $response = new Response();
        $response->setContent(json_encode([]));
        $response->headers->set('Content-Type', 'application/json');

        $this->assertEquals($response, $result);
    }
}
