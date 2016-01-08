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
    public function getAction(Request $request, $documentId, $id = null)
    {
        $crud = $this->getCrud();

        try {
            $document = $crud->read($this->getRequestRepository($request), $documentId);
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

        if ($id === null) {
            return $this->renderRest($request, $document['variants']);
        } else if (isset($document['variants'][$id])) {
            return $this->renderRest($request, $document['variants'][$id]);
        } else {
            return $this->renderError(
                $request,
                'Variant "' . $id . '" for object "' . $documentId . '" does not exist.',
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
    public function deleteAction(Request $request, $documentId, $id = null)
    {
        $crud = $this->getCrud();
        $repository = $this->getRequestRepository($request);

        $document = $crud->read($this->getRequestRepository($request), $documentId);

        if (!$document) {
            return $this->renderError($request, 'Document was not found', Response::HTTP_NOT_FOUND);
        }

        $document['_id'] = $documentId;

        if ($id === null) {
            $document['variants'] = [];
            $crud->update($repository, $document);
        } else if (isset($document['variants'][$id])) {
            unset($document['variants'][$id]);
            $document['variants'] = array_values($document['variants']);

            $crud->update($repository, $document);
        } else {
            return $this->renderError(
                $request,
                'Variant "' . $id . '" for object "' . $documentId . '" does not exist.',
                Response::HTTP_NOT_FOUND
            );
        }

        $this->getCrud()->commit($repository);

        return $this->renderRest($request, $document, Response::HTTP_OK);
    }
}