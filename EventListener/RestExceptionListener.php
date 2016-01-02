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

use ONGR\ApiBundle\Request\RestRequest;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Handling error exception once the REST request was sent.
 */
class RestExceptionListener
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
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {

        if ($this->support()) {
            if ($event->getException() instanceof NotFoundHttpException) {

                // TODO: return Error Message in JSON format

            } elseif ($event->getException() instanceof MethodNotAllowedHttpException) {

                // TODO: return Error Message in JSON format

            }
        }
    }

    /**
     * Checks if controller is supported.
     *
     *
     * @return bool
     */
    public function support()
    {

        // TODO: is this a REST request?

        return true;
    }

    /**
     * @return RestRequest
     */
    public function getRestRequest()
    {
        return $this->restRequest;
    }
}
