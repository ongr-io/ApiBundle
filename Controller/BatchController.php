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
 * Batch controller
 */
class BatchController extends Controller
{
    /**
     * Main action to process batch call.
     *
     * @param Request $request
     * @return Response
     */
    public function processAction(Request $request)
    {
        $crud = $this->get('ongr_api.crud');
        $requestSerializer = $this->get('ongr_api.request_serializer');
        $repository = $this->get($request->attributes->get('repository'));
        $documents = $requestSerializer->deserializeRequest($request);

        try {
            foreach ($documents as $document) {
                $crud->create($repository, $document);
            }
            $crud->commit($repository);
            return new Response(
                $requestSerializer->serializeRequest($request, ''),
                Response::HTTP_NO_CONTENT,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } catch (\Exception $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }
    }
}
