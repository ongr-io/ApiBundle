<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Service;

use ONGR\ApiBundle\Controller\RestControllerInterface;
use ONGR\ApiBundle\Request\RestRequest;
use ONGR\ApiBundle\Request\RestRequestProxy;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Handles multiple actions on one request.
 */
class BatchProcessor
{
    /**
     * @var CrudInterface
     */
    private $crud;

    /**
     * @param Crud $crud
     */
    public function __construct($crud)
    {
        $this->crud = $crud;
    }

    /**
     * Handles batch process.
     *
     * @param RestRequest $restRequest

     * @return array|bool Returns false on deserialization error.
     */
    public function handle(RestRequest $restRequest)
    {
        $data = $restRequest->getData();

        if ($data === null) {
            return false;
        }

        $out = [];

        foreach ($data as $batch) {

            #TODO insert to the repository bulk and commit/refresh

        }
        return $out;
    }
}
