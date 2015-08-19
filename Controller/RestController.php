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
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 */
class RestController extends AbstractApiController
{
    /**
     * {@inheritdoc}
     */
    public function postAction(Request $request)
    {
        $data = $this->validate($request);

        if ($data instanceof Response) {
            return $data;
        }

        $repository = $this->getRepository($request);
        $types = $repository->getTypes();

        $repository->getManager()->getConnection()->bulk('create', reset($types), $data);
        $repository->getManager()->commit();

        $response = $this->createResponse($request, null, Response::HTTP_CREATED);
        $response->headers->set('Location', $this->generateRestUrl($request, $data['_id']));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(Request $request)
    {
        $id = $request->attributes->get('id');
        $repository = $this->getRepository($request);

        if ($id !== null) {
            if (($data = $repository->find($id, Repository::RESULTS_ARRAY)) === null) {
                return $this->createResponse($request, null, Response::HTTP_GONE);
            }
        } else {
            $search = $repository->createSearch();

            if ($request->query->has('from')) {
                $search->setFrom($request->query->get('from'));
            }

            if ($request->query->has('size')) {
                $search->setSize($request->query->get('size'));
            }

            $data = $repository->execute($search, Repository::RESULTS_ARRAY);
        }

        return $this->createResponse($request, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(Request $request)
    {
        $data = $this->validate($request);

        if ($data instanceof Response) {
            return $data;
        }

        $repository = $this->getRepository($request);
        $types = $repository->getTypes();

        $repository->getManager()->getConnection()->bulk('update', reset($types), $data);
        $repository->getManager()->commit();

        $response = $this->createResponse($request, null, Response::HTTP_NO_CONTENT);
        $response->headers->set('Location', $this->generateRestUrl($request, $data['_id']));

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(Request $request)
    {
        $id = $request->attributes->get('id');

        if ($id === null) {
            return $this->createResponse($request, null, Response::HTTP_BAD_REQUEST);
        }

        $repository = $this->getRepository($request);
        $connection = $repository->getManager()->getConnection();
        try {
            $connection->delete(
                [
                    'id' => $id,
                    'type' => $repository->getTypes(),
                    'index' => $connection->getIndexName()
                ]
            );
        } catch (Missing404Exception $e) {
            throw $this->createNotFoundException(
                sprintf('No document found with id "%s"', $id)
            );
        }

        return $this->createResponse($request, null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param Request $request
     *
     * @return Repository
     */
    protected function getRepository(Request $request)
    {
        return $this
            ->get($request->attributes->get('manager'))
            ->getRepository($request->attributes->get('repository'));
    }

    /**
     * Collect form errors.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getFormErrors(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getFormErrors($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
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

    /**
     * Validates data for submission. Returns response on validation error.
     *
     * @param Request $request
     * @param int     $errorCode
     *
     * @return array|Response
     */
    protected function validate(Request $request, $errorCode = Response::HTTP_BAD_REQUEST)
    {
        $data = $this->deserialize($request->getContent(), $request->getContentType());
        $form = $this->createForm('ongr_api_document_type', null);

        if (($id = $request->attributes->get('id')) !== null) {
            $data['id'] = $id;
        } else {
            $id = $data['id'];
        }

        $form->submit($data);
        if (!$form->isValid()) {
            return $this->createResponse(
                $request,
                ['message' => 'Validation error!', 'errors' => $this->getFormErrors($form)],
                $errorCode
            );
        }

        $data['_id'] = $id;

        return $data;
    }
}
