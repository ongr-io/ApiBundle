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

interface BatchControllerInterface extends ApiInterface
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function batchAction(Request $request);
}
