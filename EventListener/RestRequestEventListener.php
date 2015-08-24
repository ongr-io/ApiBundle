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

use ONGR\ApiBundle\Controller\RestControllerInterface;
use ONGR\ApiBundle\Request\RestRequestProxy;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class RestRequestEventListener
{
    /**
     * @var RestRequestProxy
     */
    private $restRequestProxy;

    /**
     * @param RestRequestProxy $requestProxy
     */
    public function __construct(RestRequestProxy $requestProxy)
    {
        $this->restRequestProxy = $requestProxy;
    }

    /**
     * Injects rest request proxy into request attributes
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($this->support($event->getController()[0])) {
            $event->getRequest()->attributes->set('requestProxy', $this->getRequestProxy());
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
        return $controller instanceof RestControllerInterface;
    }

    /**
     * @return RestRequestProxy
     */
    private function getRequestProxy()
    {
        return $this->restRequestProxy;
    }
}
