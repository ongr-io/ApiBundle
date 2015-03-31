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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * CRUD implementation for Api Controller.
 *
 * Interface ApiControllerInterface
 *
 * @package ONGR\ApiBundle\Controller
 */
interface ApiControllerInterface
{
    /**
     * Create operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function postAction($endpoint, Request $request);

    /**
     * Read operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function getAction($endpoint, Request $request);

    /**
     * Update operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function putAction($endpoint, Request $request);

    /**
     * Delete operation.
     *
     * @param string  $endpoint
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction($endpoint, Request $request);
}
