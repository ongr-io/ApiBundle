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
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Abstraction for rest api controller.
 */
class AbstractCollectionController extends AbstractRestController
{

    /**
     * Mapping of the commands
     *
     * @var array
     */
    private $mapping = [
        '_all' => [
            '_controller' => 'ONGRApiBundle:Collection:all',
            'methods' => ['GET'],
            'validator' => 'allow_get_all'
        ],
        '_batch' => [
            '_controller' => 'ONGRApiBundle:Collection:batch',
            'methods' => ['POST'],
            'validator' => 'allow_batch'
        ]
    ];

    /**
     * Get the mapping of the commands
     *
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * Set mapping of commands
     *
     * @param $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $this->processMapping($mapping);
    }

    /**
     * Pre-processing the mapping data
     *
     * @param $mapping
     *
     * @return mixed
     */
    private function processMapping($mapping)
    {

        // TODO: check validation of mapping value and pre-processing

        return $mapping;
    }

    /**
     * Get all documents from a specific repository
     *
     * @param RestRequest $restRequest
     * @param Repository  $repository
     *
     * @return Response
     */
    public function allAction(Request $request, Repository $repository)
    {
        // TODO: Get all documents from a specific repository

        return $this->renderRest($request, ["Message" => "This is getAll"], Response::HTTP_ACCEPTED);
    }

    /**
     * Index multiple documents via single API request
     *
     * @param RestRequest $restRequest
     * @param Repository  $repository
     *
     * @return Response
     */
    public function batchAction(Request $request, Repository $repository)
    {
        // TODO: Index multiple documents via single API request

        return $this->renderRest($request, ["Message" => "This is postBatch"], Response::HTTP_ACCEPTED);
    }
}
