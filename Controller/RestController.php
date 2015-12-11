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
use ONGR\ApiBundle\Service\Crud;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 */
class RestController extends AbstractRestController implements RestControllerInterface, ApiInterface
{
    /**
     * {@inheritdoc}
     */
    public function postAction(RestRequest $restRequest, $id = null)
    {
        try {
            $crudService = $this->getCrudService();
            $crudService->create($restRequest->getRepository(), $restRequest->getData());
            $response = $crudService->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            return $this->renderRest(['message' => $this->trans('response.error.resource')], Response::HTTP_CONFLICT);
        }

        return $this->renderRest(
            $response,
            Response::HTTP_CREATED,
            ['Location' => $this->generateRestUrl($restRequest->getRequest(), $id)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequest $restRequest, $id)
    {
        $repository = $restRequest->getRepository();
        $crudService = $this->getCrudService();

        try {
            $data = $crudService->read($repository, $id);
        } catch (\Exception $e) {
            return $this->renderRest(null, Response::HTTP_NOT_FOUND);
        }

        return $this->renderRest($data);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequest $restRequest, $id = null)
    {
        try {
            $crudService = $this->getCrudService();
            $crudService->update($restRequest->getRepository(), $restRequest->getData());
            $response = $crudService->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            return $this->renderRest(['message' => $this->trans('response.error.resource')], Response::HTTP_CONFLICT);
        }

        return $this->renderRest(
            $response,
            Response::HTTP_NO_CONTENT,
            ['Location' => $this->generateRestUrl($restRequest->getRequest(), $id)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequest $restRequest, $id)
    {
        try {
            $crudService = $this->getCrudService();
            $crudService->delete($restRequest->getRepository(), $id);
            $response = $crudService->commit($restRequest->getRepository());
        } catch (Missing404Exception $e) {
            return $this->renderRest(
                ['message' => $this->trans('response.error.not_found', ['%id%' => $id])],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->renderRest($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * Generates rest uri from request.
     *
     * @param Request $request
     * @param string  $id
     * @param string  $method
     *
     * @return string
     */
    protected function generateRestUrl(Request $request, $id = null, $method = 'GET')
    {
        if ($this->isBatch()) {
            return null;
        }

        $route = $request->attributes->get('_route');
        $route = substr_replace($route, strtolower($method), strrpos($route, '_') + 1);

        return $this->generateUrl($route, ['id' => $id]);
    }

    /**
     * Translates message.
     *
     * @param string $message
     * @param array  $parameters
     *
     * @return string
     */
    protected function trans($message, $parameters = [])
    {
        return $this->get('translator')->trans($message, $parameters, 'ApiBundle');
    }

    /**
     * @return Crud
     */
    protected function getCrudService()
    {
        return $this->get('ongr_api.crud');
    }
}
