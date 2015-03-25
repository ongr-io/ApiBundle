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
     * @param string $serviceId
     * @param array  $params
     * @param mixed  $body
     *
     * @return Response
     */
    public function put($serviceId, $params, $body);

    /**
     * Read operation.
     *
     * @param string $serviceId
     * @param array  $params
     *
     * @return Response
     */
    public function get($serviceId, $params);

    /**
     * Update operation.
     *
     * @param string $serviceId
     * @param array  $params
     * @param mixed  $body
     *
     * @return Response
     */
    public function post($serviceId, $params, $body);

    /**
     * Delete operation.
     *
     * @param string $serviceId
     * @param array  $params
     *
     * @return Response
     */
    public function delete($serviceId, $params);
}
