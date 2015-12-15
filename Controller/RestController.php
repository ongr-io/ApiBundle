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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 */
class RestController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function postAction(RestRequest $restRequest, $id = null)
    {
        try {
//            $crudService = $this->get('ongr_api.crud');
//            $crudService->create($restRequest->getRepository(), $restRequest->getData());
//            $response = $crudService->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            #TODO return error message with error header
        }

        #TODO return array with inserted documents id's
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(RestRequest $restRequest, $id)
    {
//        $repository = $restRequest->getRepository();
//        $crudService = $this->get('ongr_api.crud');
        try {
//            $data = $crudService->read($repository, $id);
        } catch (\Exception $e) {
//            return $this->renderRest(null, Response::HTTP_NOT_FOUND);
            #TODO return error message with not found header

        }
//        return $data;
        #TODO return document
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(RestRequest $restRequest, $id = null)
    {
        try {
//            $crudService = $this->get('ongr_api.crud');
//            $crudService->update($restRequest->getRepository(), $restRequest->getData());
//            $response = $crudService->commit($restRequest->getRepository());
        } catch (\Exception $e) {
            #TODO return error message and header
        }

        #TODO return success
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(RestRequest $restRequest, $id)
    {
        try {
//            $crudService = $this->get('ongr_api.crud');
//            $crudService->delete($restRequest->getRepository(), $id);
//            $response = $crudService->commit($restRequest->getRepository());
        } catch (Missing404Exception $e) {
            #TODO return error message and header
        }

        #TODO return delete success
    }
}
