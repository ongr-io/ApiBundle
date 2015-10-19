<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Unit\EventListener;

use JMS\Serializer\EventDispatcher\Event;
use JMS\Serializer\EventDispatcher\EventDispatcher;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ONGR\ApiBundle\EventListener\JsonPrettyFormatEventListener;

class JsonPrettyFormatEventListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $subscriber
     */
    private $subscriber;

    /**
     * @var $request
     */
    private $request;

    /**
     * Setup tests.
     */
    protected function setUp()
    {
        $this->request = $this->getMock('ONGR\ApiBundle\Controller\RestController');
        $this->subscriber = new JsonPrettyFormatEventListener($this->request);
    }

    /**
     * Test getSubscribedEvents function.
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals(
            [
                '0' => [
                    'event' => 'serializer.pre_serialize',
                    'method' => 'onPreSerialize',
                    'format' => 'json',
                ],
            ],
            $this->subscriber->getSubscribedEvents()
        );
    }
}
