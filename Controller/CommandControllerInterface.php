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
 * Interface with required methods for command controller.
 */
interface CommandControllerInterface
{
    /**
     * Handles index creation action.
     *
     * @param Request $request
     * @param string  $manager
     *
     * @return Response
     */
    public function createIndexAction(Request $request, $manager);

    /**
     * Handles index drop action.
     *
     * @param Request $request
     * @param string  $manager
     *
     * @return Response
     */
    public function dropIndexAction(Request $request, $manager);

    /**
     * Handles schema update action.
     *
     * @param Request $request
     * @param string  $manager
     *
     * @return Response
     */
    public function updateSchemaAction(Request $request, $manager);
}
