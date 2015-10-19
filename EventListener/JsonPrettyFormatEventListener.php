<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\EventListener;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use ONGR\ApiBundle\Request\RestRequest;

/**
 * Listener for injecting pretty json format options.
 */
class JsonPrettyFormatEventListener implements EventSubscriberInterface
{
    /**
     * @var RestRequest
     */
    private $restRequest;

    /**
     * @param RestRequest $restRequest
     */
    public function __construct($restRequest)
    {
        $this->restRequest = $restRequest;
    }

    /**
     * Subscribed Events.
     */
    public static function getSubscribedEvents()
    {
        return [
            [
                'event' => 'serializer.pre_serialize',
                'method' => 'onPreSerialize',
                'format' => 'json',
            ],
        ];
    }

    /**
     * Set pretty json options.
     *
     * @param ObjectEvent $event
     */
    public function onPreSerialize(ObjectEvent $event)
    {
        if (null !== $this->restRequest->get('pretty')) {
            $event->getVisitor()->setOptions(128);
        }
    }
}
