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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD interface for Api Controller.
 */
interface ApiControllerInterface
{
    /**
     * Create operation.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request);

    /**
     * Read operation.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function getAction(Request $request);

    /**
     * Update operation.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function putAction(Request $request);

    /**
     * Delete operation.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request);
}
