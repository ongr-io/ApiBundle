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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AbstractRestController extends Controller
{
    /**
     * Renders rest response.
     *
     * @param mixed $data
     * @param int   $statusCode
     *
     * @return Response
     */
    public function renderRest($data, $statusCode = Response::HTTP_OK)
    {
        return $this->get('ongr_api.rest_response_view_handler')->handleView($data, $statusCode);
    }
}
