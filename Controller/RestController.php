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
use ONGR\ApiBundle\Request\RestRequestProxy;
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
    public function postAction(RestRequestProxy $requestProxy, $id = null)
    {
        $data = $requestProxy->getData();
        $validator = $this->get('ongr_api.rest.validator');

        if ($id !== null) {
            $data['id'] = $id;
        } else {
            $id = $data['id'];
        }

        if (!$validator->validate($data)) {
            return $this->renderRest(
                ['message' => 'Validation error!', 'errors' => $validator->getErrors()],
                Response::HTTP_BAD_REQUEST
            );
        }

        $repository = $requestProxy->getRepository();
        $types = $repository->getTypes();

        $data['_id'] = $id;
        $repository->getManager()->getConnection()->bulk('create', reset($types), $data);
        $repository->getManager()->commit();

        $response = $this->renderRest(null, Response::HTTP_CREATED);
        $response->headers->set('Location', $this->generateRestUrl($requestProxy->getRequest(), $id));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequestProxy $requestProxy, $id = null)
    {
        if ($id !== null) {
            if (($data = $requestProxy->getRepository()->find($id, Repository::RESULTS_ARRAY)) === null) {
                return $this->renderRest(null, Response::HTTP_GONE);
            }
        } else {
            $search = new Search();
            if ($requestProxy->query->has('from')) {
                $search->setFrom($requestProxy->query->get('from'));
            }
            if ($requestProxy->query->has('size')) {
                $search->setSize($requestProxy->query->get('size'));
            }
            $data = $requestProxy->getRepository()->execute($search, Repository::RESULTS_ARRAY);
        }

        return $this->renderRest($data);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequestProxy $requestProxy, $id = null)
    {
        $data = $requestProxy->getData();
        $validator = $this->get('ongr_api.rest.validator');

        if ($id !== null) {
            $data['id'] = $id;
        } else {
            $id = $data['id'];
        }

        if (!$validator->validate($data)) {
            return $this->renderRest(
                [
                    'message' => 'Validation error!',
                    'errors' => $validator->getErrors()
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $repository = $requestProxy->getRepository();
        $types = $repository->getTypes();

        $data['_id'] = $id;
        $repository->getManager()->getConnection()->bulk('index', reset($types), $data);
        $repository->getManager()->commit();

        $response = $this->renderRest(null, Response::HTTP_NO_CONTENT);
        $response->headers->set('Location', $this->generateRestUrl($requestProxy->getRequest(), $id));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequestProxy $requestProxy, $id = null)
    {
        if ($id === null) {
            return $this->renderRest(null, Response::HTTP_BAD_REQUEST);
        }

        $connection = $requestProxy->getRepository()->getManager()->getConnection();
        try {
            $connection->delete(
                [
                    'id' => $id,
                    'type' => $requestProxy->getRepository()->getTypes(),
                    'index' => $connection->getIndexName()
                ]
            );
        } catch (Missing404Exception $e) {
            throw $this->createNotFoundException(
                sprintf('No document found with id "%s"', $id)
            );
        }

        return $this->renderRest(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     * @param string  $id
     * @param string  $method
     *
     * @return string
     */
    protected function generateRestUrl(Request $request, $id = null, $method = 'GET')
    {
        $route = $request->attributes->get('_route');
        $route = substr_replace($route, strtolower($method), strrpos($route, '_') + 1);

        return $this->generateUrl($route, ['id' => $id]);
    }
}
