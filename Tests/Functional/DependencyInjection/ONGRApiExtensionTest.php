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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ApiBundle\DependencyInjection\ONGRApiExtension;
use ONGR\ApiBundle\Service\DataRequestService;

/**
 * Tests for ONGRApiExtension.
 */
class ONGRApiExtensionTest extends WebTestCase
{
    /**
     * Check services are  created.
     */
    public function testService()
    {
        // First load up the default variables and check if they're set.
        $kernel = static::createClient()->getKernel();
        $container = $kernel->getContainer();

        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('test', 'magic')
        );

        /** @var DataRequestService $dataRequest */
        $dataRequest = $container->get($serviceName);

        $result = $dataRequest->get([]);

        $this->assertEquals([], $result);

        $serviceName = ONGRApiExtension::getServiceNameWithNamespace(
            'data_request',
            ONGRApiExtension::getNamespaceName('test', 'black_magic')
        );

        /** @var DataRequestService $dataRequest */
        $dataRequest = $container->get($serviceName);

        $result = $dataRequest->get([]);

        $this->assertEquals([], $result);
    }
}
