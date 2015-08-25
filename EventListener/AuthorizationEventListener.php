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

use ONGR\ApiBundle\Response\ViewHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class AuthorizationEventListener
{
    /**
     * @var ContainerInterface
     *
     * @internal View handler cannot be initialized before RouteListener if request is valid.
     */
    private $container;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param ContainerInterface $container
     * @param string             $secret
     */
    public function __construct(ContainerInterface $container, $secret)
    {
        $this->container = $container;
        $this->secret = $secret;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$event->isMasterRequest() || strpos($request->attributes->get('_route', ''), 'ongr_api_') === false) {
            return;
        }

        if ($request->headers->get('authorization') !== $this->getSecret()) {
            $event->setResponse(
                $this
                    ->getViewHandler()
                    ->handleView(
                        ['message' => 'Authorization error!', 'error' => 'Invalid authorization secret token.'],
                        Response::HTTP_UNAUTHORIZED
                    )
            );
        }
    }

    /**
     * @return ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('ongr_api.rest_response_view_handler');
    }

    /**
     * @return string
     */
    private function getSecret()
    {
        return $this->secret;
    }
}
