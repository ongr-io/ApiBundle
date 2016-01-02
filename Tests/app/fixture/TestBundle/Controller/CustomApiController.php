<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\app\fixture\TestBundle\Controller;

use ONGR\ApiBundle\Controller\RestControllerInterface;
use ONGR\ApiBundle\Request\RestRequest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Custom Api Controller.
 */
class CustomApiController extends Controller implements RestControllerInterface
{
    /**
     * Create operation.
     *
     * @param RestRequest $request
     * @param string $endpoint
     *
     * @return Response
     */
    public function postAction(RestRequest $request, $endpoint = null)
    {
        return new JsonResponse('Custom controller POST', Response::HTTP_OK);
    }

    /**
     * Read operation.
     *
     * @param RestRequest $request
     * @param string $endpoint
     *
     * @return Response
     */
    public function getAction(RestRequest $request, $endpoint = null)
    {
        return new JsonResponse('Custom controller GET', Response::HTTP_OK);
    }

    /**
     * Update operation.
     *
     * @param RestRequest $request
     * @param string $endpoint
     *
     * @return Response
     */
    public function putAction(RestRequest $request, $endpoint = null)
    {
        return new JsonResponse('Custom controller PUT', Response::HTTP_OK);
    }

    /**
     * Delete operation.
     *
     * @param Request $request
     * @param string $endpoint
     *
     * @return Response
     */
    public function deleteAction(RestRequest $request, $endpoint = null)
    {
        return new JsonResponse('Custom controller DELETE', Response::HTTP_OK);
    }
}
