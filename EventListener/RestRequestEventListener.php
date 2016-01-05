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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

/**
 * Replaces repository identifier by actual repository service.
 */
class RestRequestEventListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Replaces repository identifier by actual repository service.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getRequest()->attributes->has('repository')) {
            $event->getRequest()->attributes->set(
                'repository',
                $this->container->get(
                    $event->getRequest()->attributes->get('repository')
                )
            );
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
}
