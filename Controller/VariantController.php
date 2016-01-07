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
class VariantController extends AbstractRestController implements RestControllerInterface
{
    /**
     * @inheritDoc
     */
    public function postAction(Request $request, $id = null)
    {

    }

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