<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Controller;

use JMS\Serializer\SerializerBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 *
 * Abstract class ApiControllerInterface
 *
 * @package ONGR\ApiBundle\Controller
 */
class ApiController extends Controller implements ApiControllerInterface
{
    /**
     * Create operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function putAction($endpoint, Request $request)
    {
        return new Response('Not implemented');
    }

    /**
     * Read operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function getAction($endpoint, Request $request)
    {
        $service = $this->get($endpoint);
        $serializer = SerializerBuilder::create()->build();

        $data = $service->get($request);
        $data = $serializer->serialize($data, 'json');

        $response = new Response();
        $response->setContent($data);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Update operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function postAction($endpoint, Request $request)
    {
        return new Response('Not implemented');
    }

    /**
     * Delete operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction($endpoint, Request $request)
    {
        return new Response('Not implemented');
    }
}
