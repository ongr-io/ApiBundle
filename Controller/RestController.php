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
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 */
class RestController extends AbstractRestController implements RestControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function postAction(RestRequest $restRequest, $id = null)
    {
        $validator = $this->get('ongr_api.rest.validator');

        $data = $validator->validate($restRequest);
        if ($data === false) {
            return $this->renderRest(
                ['message' => 'Validation error!', 'errors' => $validator->getErrors()],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($id === null && !isset($data['id'])) {
            return $this->renderRest(
                ['message' => 'No identifier found!'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($id !== null) {
            $data['id'] = $id;
        } else {
            $id = $data['id'];
        }

        $repository = $restRequest->getRepository();
        $types = $repository->getTypes();

        $data['_id'] = $id;
        $repository->getManager()->getConnection()->bulk('create', reset($types), $data);
        $repository->getManager()->commit();

        return $this->renderRest(
            null,
            Response::HTTP_CREATED,
            ['Location' => $this->generateRestUrl($restRequest->getRequest(), $id)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequest $restRequest, $id = null)
    {
        if ($id !== null) {
            $data = $restRequest->getRepository()->find($id, Repository::RESULTS_ARRAY);
            if ($data === null) {
                return $this->renderRest(null, Response::HTTP_GONE);
            }
        } else {
            $search = new Search();
            if ($restRequest->query->has('from')) {
                $search->setFrom($restRequest->query->get('from'));
            }
            if ($restRequest->query->has('size')) {
                $search->setSize($restRequest->query->get('size'));
            }
            $data = $restRequest->getRepository()->execute($search, Repository::RESULTS_ARRAY);
        }

        return $this->renderRest($data);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequest $restRequest, $id = null)
    {
        $data = $restRequest->getData();
        $validator = $this->get('ongr_api.rest.validator');
        $data = $validator->validate($restRequest);

        if ($data === false) {
            return $this->renderRest(
                [
                    'message' => 'Validation error!',
                    'errors' => $validator->getErrors(),
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($id === null && !isset($data['id'])) {
            return $this->renderRest(
                ['message' => 'No identifier found!'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($id !== null) {
            $data['id'] = $id;
        } else {
            $id = $data['id'];
        }

        $repository = $restRequest->getRepository();
        $types = $repository->getTypes();

        $data['_id'] = $id;
        $repository->getManager()->getConnection()->bulk('index', reset($types), $data);
        $repository->getManager()->commit();

        return $this->renderRest(
            null,
            Response::HTTP_NO_CONTENT,
            ['Location' => $this->generateRestUrl($restRequest->getRequest(), $id)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequest $restRequest, $id = null)
    {
        if ($id === null) {
            return $this->renderRest(null, Response::HTTP_BAD_REQUEST);
        }

        $connection = $restRequest->getRepository()->getManager()->getConnection();
        try {
            $connection->delete(
                [
                    'id' => $id,
                    'type' => $restRequest->getRepository()->getTypes(),
                    'index' => $connection->getIndexName(),
                ]
            );
        } catch (Missing404Exception $e) {
            return $this->renderRest(
                ['message' => sprintf('No document found with id "%s"', $id)],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->renderRest(null, Response::HTTP_NO_CONTENT);
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
}
