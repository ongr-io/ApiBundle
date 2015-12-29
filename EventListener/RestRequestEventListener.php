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

use ONGR\ApiBundle\Controller\CollectionController;
use ONGR\ApiBundle\Controller\RestController;
use ONGR\ApiBundle\Request\RestRequest;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Listener for injecting rest request into controller.
 */
class RestRequestEventListener
{
    /**
     * @var RestRequest
     */
    private $restRequest;

    /**
     * @param RestRequest $restRequest
     */
    public function __construct(RestRequest $restRequest)
    {
        $this->restRequest = $restRequest;
    }

    /**
     * Injects rest request proxy into request attributes.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($this->support($event->getController()[0])) {
            $event->getRequest()->attributes->set('restRequest', $this->getRestRequest());
        }
    }

    /**
     * Checks if controller is supported.
     *
     * @param object $controller
     *
     * @return bool
     */
    public function support($controller)
    {
        return ($controller instanceof RestController || $controller instanceof CollectionController);
    }

    /**
     * @return RestRequest
     */
    public function getRestRequest()
    {
        return $this->restRequest;
    }
}
