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

use ONGR\ApiBundle\Request\RestRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 */
class RestController extends AbstractRestController implements
    RestControllerInterface
{

    /**
     * {@inheritdoc}
     */
    public function postAction(RestRequest $restRequest, $id = null)
    {
        try {
            $this->getCrud()->create($restRequest->getRepository(), $restRequest->getData());
            $response = $this->getCrud()->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            // TODO: 406 validation error, 409 resource exists.
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->renderRest($response, Response::HTTP_OK);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequest $restRequest, $id)
    {
        try {
            $data = $this->getCrud()->read($restRequest->getRepository(), $id);
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return $this->renderRest($data, Response::HTTP_OK);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequest $restRequest, $id = null)
    {
        try {
            if ($id !== null) {
                $data['_id'] = $id;
            }

            $this->getCrud()->update($restRequest->getRepository(), $data);
            $response = $this->getCrud()->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            // TODO: 406 validation error
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->renderRest($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequest $restRequest, $id)
    {
        try {
            $this->getCrud()->delete($restRequest->getRepository(), $id);
            $response = $this->getCrud()->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            // TODO: 404 if not found.
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->renderRest($response, Response::HTTP_NO_CONTENT);
    }
}
