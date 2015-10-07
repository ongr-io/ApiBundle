<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Request;

use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Factory for rest request.
 */
class RestRequestFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->requestStack = $this->container->get('request_stack');
    }

    /**
     * Instantiates rest request proxy object.
     *
     * @return RestRequest
     */
    public function get()
    {
        return new RestRequest(
            $this->getRequest(),
            $this->container->get('serializer'),
            $this->getRepository()
        );
    }

    /**
     * @return Repository|null
     */
    private function getRepository()
    {
        if ($this->getRequest()->attributes->has('manager')
            && $this->getRequest()->attributes->has('repository')
        ) {
            return $this
                ->container
                ->get($this->getRequest()->attributes->get('manager'))
                ->getRepository($this->getRequest()->attributes->get('repository'));
        }

        return null;
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request !== null ? $request : Request::createFromGlobals();
    }
}
