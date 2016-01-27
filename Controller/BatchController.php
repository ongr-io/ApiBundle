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
     * Main action to process batch call.
     *
     * @param Request $request
     * @return Response
     */
    public function processAction(Request $request)
    {
        $crud = $this->getCrudService();
        $repository = $this->getRequestRepository($request);
        $documents = $this->get('ongr_api.request_serializer')->deserializeRequest($request);

        try {
            foreach ($documents as $document) {
                $crud->create($repository, $document);
            }
            $crud->commit($repository);
            return $this->renderRest($request, '', Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
