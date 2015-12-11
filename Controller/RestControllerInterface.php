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
 * CRUD interface for Rest Api Controller.
 */
interface RestControllerInterface
{
    /**
     * Create operation.
     *
     * @param RestRequest $restRequest
     * @param string      $id
     *
     * @return Response
     */
    public function postAction(RestRequest $restRequest, $id = null);

    /**
     * Read operation.
     *
     * @param RestRequest $restRequest
     * @param string      $id
     *
     * @return Response
     */
    public function getAction(RestRequest $restRequest, $id);

    /**
     * Update operation.
     *
     * @param RestRequest $restRequest
     * @param string      $id
     *
     * @return Response
     */
    public function putAction(RestRequest $restRequest, $id = null);

    /**
     * Delete operation.
     *
     * @param RestRequest $restRequest
     * @param string      $id
     *
     * @return Response
     */
    public function deleteAction(RestRequest $restRequest, $id);
}
