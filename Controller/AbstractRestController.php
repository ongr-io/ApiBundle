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

use ONGR\ApiBundle\Service\Crud;
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstraction for rest api controller.
 */
class AbstractRestController extends Controller
{
    /**
     * Get CRUD Service
     *
     * @return Crud
     */
    protected function getCrudService()
    {
        return $this->get('ongr_api.crud');
    }

    /**
     * Renders rest response.
     *
     * @param Request $request
     * @param string  $data
     * @param int     $statusCode
     * @param array   $headers
     *
     * @return Response|array
     */
    protected function renderRest(
        $request,
        $data,
        $statusCode = Response::HTTP_OK,
        $headers = []
    ) {
        $requestSerializer = $this->get('ongr_api.request_serializer');

        return new Response(
            $requestSerializer->serializeRequest($request, $data),
            $statusCode,
            array_merge(
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)],
                $headers
            )
        );
    }

    /**
     * Error Response
     *
     * @param Request $request
     * @param string  $message
     * @param int     $statusCode
     *
     * @return Response
     */
    protected function renderError(
        $request,
        $message,
        $statusCode = Response::HTTP_BAD_REQUEST
    ) {

        // TODO: Add more information about this Error

        $response = [
            'errors' => [],
            'message' => $message,
            'code' => $statusCode
        ];

        return $this->renderRest($request, $response, $statusCode);
    }

    /**
     * Returns repository object from it's identifier in request.
     *
     * @param Request $request
     *
     * @return Repository
     */
    protected function getRequestRepository(Request $request)
    {
        return $this->get($request->attributes->get('repository'));
    }
}
