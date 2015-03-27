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
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function putData($request, $endpoint)
    {
        return new Response('Not implemented');
    }

    /**
     * Read operation.

     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function getData($request, $endpoint)
    {
        return new Response('Not implemented.');
    }

    /**
     * Update operation.
     *
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function postData($request, $endpoint)
    {
        return new Response('Not implemented');
    }

    /**
     * Delete operation.
     *
     * @param Request $request
     * @param string  $endpoint
     *
     * @return Response
     */
    public function deleteData($request, $endpoint)
    {
        return new Response('Not implemented');
    }
}
