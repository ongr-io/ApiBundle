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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Batch controller
 */
class BatchController extends AbstractRestController
{
    /**
     * Action to process create batch call.
     *
     * @param Request $request
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $data = $this->get('ongr_api.batch_request_handler')->handleRequest(
                $request,
                $repository = $this->getRequestRepository($request),
                'create'
            );
            return $this->renderRest($request, $data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Action to process put batch call.
     *
     * @param Request $request
     * @return Response
     */
    public function putAction(Request $request)
    {
        try {
            $data = $this->get('ongr_api.batch_request_handler')->handleRequest(
                $request,
                $repository = $this->getRequestRepository($request),
                'update'
            );
            return $this->renderRest($request, $data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Action to process delete batch call.
     *
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        try {
            $data = $this->get('ongr_api.batch_request_handler')->handleRequest(
                $request,
                $repository = $this->getRequestRepository($request),
                'delete'
            );
            return $this->renderRest($request, $data, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
