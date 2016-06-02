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
 *
 * @deprecated since 1.0. Will be removed in 2.0. Use a custom controller for your implementation of variants.
 */
class VariantController extends AbstractRestController
{
    /**
     * @inheritDoc
     */
    public function getAction(Request $request, $documentId, $variantId = null)
    {
        $crud = $this->getCrudService();

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

        if ($variantId === null) {
            return $this->renderRest($request, $document['variants']);
        } elseif (isset($document['variants'][$variantId])) {
            return $this->renderRest($request, $document['variants'][$variantId]);
        } else {
            return $this->renderError(
                $request,
                'Variant "' . $variantId . '" for object "' . $documentId . '" does not exist.',
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

        $document = $this->getCrudService()->read($repository, $documentId);

        if (!$document) {
            return $this->renderError($request, 'Document was not found', Response::HTTP_NOT_FOUND);
        }

        $document['variants'] = $this->get('ongr_api.request_serializer')->deserializeRequest($request);

        try {
            $this->getCrudService()->update($repository, $documentId, $document);
            $this->getCrudService()->commit($repository);
        } catch (\RuntimeException $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->renderError($request, $e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        $row = $this->getCrudService()->read($repository, $documentId);
        return $this->renderRest($request, $row, Response::HTTP_CREATED);
    }

    /**
     * @inheritDoc
     */
    public function putAction(Request $request, $documentId, $variantId)
    {
        if ($variantId === null) {
            return $this->renderError(
                $request,
                'You must provide variant id in your request.',
                Response::HTTP_BAD_REQUEST
            );
        }

        $crud = $this->getCrudService();
        $repository = $this->getRequestRepository($request);

        $document = $crud->read($repository, $documentId);

        if (!$document) {
            return $this->renderError($request, 'Document was not found', Response::HTTP_NOT_FOUND);
        }

        if (!isset($document['variants'][$variantId])) {
            return $this->renderError(
                $request,
                'Variant "' . $variantId . '" for object "' . $documentId . '" does not exist.',
                Response::HTTP_NOT_FOUND
            );
        }

        $document['variants'][$variantId] = $this->get('ongr_api.request_serializer')->deserializeRequest($request);

        $crud->update($repository, $documentId, $document);
        $crud->commit($repository);

        return $this->renderRest($request, $document, Response::HTTP_OK);
    }

    /**
     * @inheritDoc
     */
    public function deleteAction(Request $request, $documentId, $variantId = null)
    {
        $crud = $this->getCrudService();
        $repository = $this->getRequestRepository($request);

        $document = $crud->read($repository, $documentId);

        if (!$document) {
            return $this->renderError($request, 'Document was not found', Response::HTTP_NOT_FOUND);
        }

        if ($variantId === null) {
            $document['variants'] = [];
            $crud->update($repository, $documentId, $document);
        } elseif (isset($document['variants'][$variantId])) {
            unset($document['variants'][$variantId]);
            $document['variants'] = array_values($document['variants']);

            $crud->update($repository, $documentId, $document);
        } else {
            return $this->renderError(
                $request,
                'Variant "' . $variantId . '" for object "' . $documentId . '" does not exist.',
                Response::HTTP_NOT_FOUND
            );
        }

        $crud->commit($repository);

        return $this->renderRest($request, $document, Response::HTTP_OK);
    }
}
