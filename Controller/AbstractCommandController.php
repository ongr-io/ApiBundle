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
 * Abstraction for rest api controller.
 */
class AbstractCommandController extends AbstractRestController
{

    /**
     * Mapping of the commands
     *
     * @var array
     */
    private $mapping = [
        '_all' => [
            '_controller' => 'ONGRApiBundle:Command:all',
            'methods' => ['GET'],
            'enable' => 'allow_get_all'
        ],
        '_batch' => [
            '_controller' => 'ONGRApiBundle:Command:batch',
            'methods' => ['POST'],
            'enable' => 'allow_batch'
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
     *
     * @return Response
     */
    public function allAction(RestRequest $restRequest)
    {
        // TODO: Get all documents from a specific repository

        return $this->renderRest(["Message" => "This is getAll"], Response::HTTP_ACCEPTED);
    }

    /**
     * Index multiple documents via single API request
     *
     * @param RestRequest $restRequest
     *
     * @return Response
     */
    public function batchAction(RestRequest $restRequest)
    {
        // TODO: Index multiple documents via single API request

        return $this->renderRest(["Message" => "This is postBatch"], Response::HTTP_ACCEPTED);
    }
}
