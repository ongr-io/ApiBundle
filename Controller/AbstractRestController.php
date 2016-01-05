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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstraction for rest api controller.
 */
class AbstractRestController extends Controller
{

    /** @var  Crud $crud */
    private $crud;

    /**
     * Get CRUD Service
     *
     * @return Crud
     */
    public function getCrud()
    {
        if (!$this->crud) {
            if (!$this->container->has('ongr_api.crud')) {
                throw new \RuntimeException('Please set RESTful CRUD Service.');
            }
            $this->crud = $this->container->get('ongr_api.crud');
        }

        return $this->crud;
    }

    /**
     * Set CRUD Service
     *
     * @param Crud $crud
     */
    public function setCrud(Crud $crud)
    {
        $this->crud = $crud;
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
    public function renderRest(
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
    public function renderError(
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
}
