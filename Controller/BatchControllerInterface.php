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

interface BatchControllerInterface extends ApiInterface
{
    /**
     * @param RestRequest $restRequest
     *
     * @return Response
     */
    public function batchAction(RestRequest $restRequest);
}
