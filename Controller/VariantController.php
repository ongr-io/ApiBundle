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
 * This controller works with document variants.
 */
class VariantController extends AbstractRestController
{
    /**
     * @inheritDoc
     */
    public function getAction(Request $request, $id, $variantId = null)
    {
        $crud = $this->getCrud();

        try {
            $document = $crud->read($this->getRequestRepository($request), $id);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        if (!isset($document['variants'])) {
            return $this->renderError(
                $request,
                'Document does not support variants.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        if ($variantId === null) {
            return $this->renderRest($request, $document['variants']);
        } else if (isset($document['variants'][$variantId])) {
            return $this->renderRest($request, $document['variants'][$variantId]);
        } else {
            return $this->renderError(
                $request,
                'Variant "' . $variantId . '" for object "' . $id . '" does not exist.',
                Response::HTTP_NOT_FOUND
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function postAction(Request $request, $documentId)
    {
        $repository = $this->getRequestRepository($request);

        $document = $this->getCrud()->read($this->getRequestRepository($request), $documentId);

        if (!$document) {
            return $this->renderError($request, 'Document was not found', Response::HTTP_NOT_FOUND);
        }

        $document['_id'] = $documentId;
        $document['variants'] = $this->get('ongr_api.request_serializer')->deserializeRequest($request);

        try {
            $this->getCrud()->update($repository, $document);
            $this->getCrud()->commit($repository);
        } catch (\RuntimeException $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $row = $this->getCrud()->read($repository, $documentId);
        return $this->renderRest($request, $row, Response::HTTP_CREATED);
    }

    /**
     * @inheritDoc
     */
    public function putAction(Request $request, $id)
    {
        // TODO: Implement putAction() method.
    }

    /**
     * @inheritDoc
     */
    public function deleteAction(Request $request, $id)
    {
        // TODO: Implement deleteAction() method.
    }
}