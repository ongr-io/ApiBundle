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

use Elasticsearch\Common\Exceptions\NoDocumentsToGetException;
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
    public function getAction(RestRequest $restRequest, $id)
    {
        try {
            $row = $this->getCrud()->read($restRequest->getRepository(), $id);
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->renderRest($row, Response::HTTP_OK);
    }

    /**
     * {@inheritdoc}
     */
    public function postAction(RestRequest $restRequest, $id = null)
    {
        $data = $restRequest->getData();
        if (!empty($id)) {
            $data['_id'] = $id;
        }

        // TODO: check validation

        try {
            $this->getCrud()->create($restRequest->getRepository(), $data);
            $response = $this->getCrud()->commit($restRequest->getRepository());
        } catch (\RuntimeException $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_CONFLICT);
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        if ($response['errors']) {
            // TODO: 406 validation error
        }

        $id = $response['items'][0]['create']['_id'];
        $row = $this->getCrud()->read($restRequest->getRepository(), $id);
        return $this->renderRest($row, Response::HTTP_CREATED);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequest $restRequest, $id)
    {

        $data = $restRequest->getData();
        if (!empty($id)) {
            $data['_id'] = $id;
        }

        // TODO: check validation

        try {
            $this->getCrud()->update($restRequest->getRepository(), $data);
            $response = $this->getCrud()->commit($restRequest->getRepository());
        } catch (\RuntimeException $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST); // Missing _id
        } catch (NoDocumentsToGetException $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        if ($response['errors']) {
            // TODO: 406 validation error
        }

        $id = $response['items'][0]['update']['_id'];
        $row = $this->getCrud()->read($restRequest->getRepository(), $id);
        return $this->renderRest($row, Response::HTTP_NO_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequest $restRequest, $id)
    {

        $data = $restRequest->getData();
        if (!empty($id)) {
            $data['_id'] = $id;
        }

        try {
            $this->getCrud()->delete($restRequest->getRepository(), $id);
            $response = $this->getCrud()->commit($restRequest->getRepository());
        } catch (\RuntimeException $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST); // Missing _id
        } catch (NoDocumentsToGetException $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->renderError($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->renderRest($response, Response::HTTP_NO_CONTENT);
    }
}
