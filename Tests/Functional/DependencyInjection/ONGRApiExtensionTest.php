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
    public function testService()
    {
        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('test', 'magic')
        );

        /** @var DataRequestService $dataRequest */
        $dataRequest = $this->getContainer()->get($serviceName);

        $result = $dataRequest->get([]);

        $this->assertEquals([], $result);

//        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
//            'data_request',
//            ONGRApiExtension::getNamespaceName('test', 'black_magic')
//        );
//
//        /** @var DataRequestService $dataRequest */
//        $dataRequest = $this->getContainer->get($serviceName);
//
//        $result = $dataRequest->get([]);
//
//        $this->assertEquals([], $result);
    }
}
