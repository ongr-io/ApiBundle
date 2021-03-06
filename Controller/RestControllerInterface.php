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
 * CRUD interface for Rest Api Controller.
 */
interface RestControllerInterface
{
    /**
     * Create operation.
     *
     * @param Request $request
     * @param string  $documentId
     *
     * @return Response
     */
    public function postAction(Request $request, $documentId = null);

    /**
     * Read operation.
     *
     * @param Request $request
     * @param string  $documentId
     *
     * @return Response
     */
    public function getAction(Request $request, $documentId);

    /**
     * Update operation.
     *
     * @param Request $request
     * @param string  $documentId
     *
     * @return Response
     */
    public function putAction(Request $request, $documentId);

    /**
     * Delete operation.
     *
     * @param Request $request
     * @param string  $documentId
     *
     * @return Response
     */
    public function deleteAction(Request $request, $documentId);
}
