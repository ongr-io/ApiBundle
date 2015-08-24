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

use ONGR\ApiBundle\Request\RestRequestProxy;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD interface for Rest Api Controller.
 */
interface RestControllerInterface extends ApiInterface
{
    /**
     * Create operation.
     *
     * @param RestRequestProxy $requestProxy
     * @param srting   $id
     *
     * @return Response
     */
    public function postAction(RestRequestProxy $requestProxy, $id = null);

    /**
     * Read operation.
     *
     * @param RestRequestProxy $requestProxy
     * @param srting   $id
     *
     * @return Response
     */
    public function getAction(RestRequestProxy $requestProxy, $id = null);

    /**
     * Update operation.
     *
     * @param RestRequestProxy $requestProxy
     * @param srting   $id
     *
     * @return Response
     */
    public function putAction(RestRequestProxy $requestProxy, $id = null);

    /**
     * Delete operation.
     *
     * @param RestRequestProxy $requestProxy
     * @param srting   $id
     *
     * @return Response
     */
    public function deleteAction(RestRequestProxy $requestProxy, $id = null);
}
