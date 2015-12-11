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

class ONGRApiExtensionTest extends WebTestCase
{
    /**
     * Data provider for test services.
     *
     * @return array
     */
    public function getTestServicesData()
    {
        return [
            [
                'ongr_api.elasticsearch_loader',
                'ONGR\ApiBundle\Routing\ElasticsearchLoader',
            ],
            [
                'ongr_api.rest.validator',
                'ONGR\ApiBundle\Validator\DocumentValidator',
            ],
            [
                'ongr_api.event_listener.rest_request',
                'ONGR\ApiBundle\EventListener\RestRequestEventListener',
                false,
            ],
            [
                'ongr_api.event_listener.authentication',
                'ONGR\ApiBundle\EventListener\AuthorizationEventListener',
            ],
            [
                'ongr_api.rest_request_factory',
                'ONGR\ApiBundle\Request\RestRequestFactory',
            ],
            [
                'ongr_api.rest_request',
                'ONGR\ApiBundle\Request\RestRequest',
                false,
            ],
            [
                'ongr_api.rest_response_view_handler',
                'ONGR\ApiBundle\Response\ViewHandler',
                false,
            ],
            [
                'ongr_api.batch_processor',
                'ONGR\ApiBundle\Service\BatchProcessor',
            ],
            [
                'ongr_api.rest_controller',
                'ONGR\ApiBundle\Controller\RestController',
            ],
            [
                'ongr_api.batch_controller',
                'ONGR\ApiBundle\Controller\BatchController',
            ],
        ];
    }

    /**
     * Tests if all services exists.
     *
     * @param string $id
     * @param string $instance
     * @param bool   $fetch
     *
     * @dataProvider getTestServicesData()
     */
    public function testServices($id, $instance, $fetch = true)
    {
        $container = static::createClient()->getContainer();

        $this->assertTrue($container->has($id));
        if ($fetch) {
            $this->assertInstanceOf($instance, $container->get($id));
        }
    }
}
