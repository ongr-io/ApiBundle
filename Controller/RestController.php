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

use Elasticsearch\Common\Exceptions\Missing404Exception;
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
            // $crudService = $this->get('ongr_api.crud');
            // $crudService->create($restRequest->getRepository(), $restRequest->getData());
            // $response = $crudService->commit($restRequest->getRepository());

            // TODO: POST operation

        } catch (\Exception $e) {
            // TODO return error message with error header
        }

        return $this->renderRest(["Message" => "This is POST"], Response::HTTP_NOT_FOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequest $restRequest, $id)
    {

        // $repository = $restRequest->getRepository();
        // $crudService = $this->get('ongr_api.crud');
        try {
            // $data = $crudService->read($repository, $id);

            // TODO: POST operation

        } catch (\Exception $e) {
            return $this->renderRest(null, Response::HTTP_NOT_FOUND);
            #TODO return error message with not found header

        }

        return $this->renderRest(["Message" => "This is GET"], Response::HTTP_NOT_FOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequest $restRequest, $id = null)
    {
        try {
            // $crudService = $this->get('ongr_api.crud');
            // $crudService->update($restRequest->getRepository(), $restRequest->getData());
            // $response = $crudService->commit($restRequest->getRepository());

            // TODO: PUT operation

        } catch (\Exception $e) {
            // TODO: return error message and header
        }

        return $this->renderRest(["Message" => "This is PUT"], Response::HTTP_NOT_FOUND);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequest $restRequest, $id)
    {
        try {
            // $crudService = $this->get('ongr_api.crud');
            // $crudService->delete($restRequest->getRepository(), $id);
            // $response = $crudService->commit($restRequest->getRepository());

        } catch (Missing404Exception $e) {
            // TODO: return error message and header
        }

        return $this->renderRest(["Message" => "This is DELETE"], Response::HTTP_NOT_FOUND);
    }
}
