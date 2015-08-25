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
     * @var bool
     */
    private $batch = false;

    /**
     * @return boolean
     */
    public function isBatch()
    {
        return $this->batch;
    }

    /**
     * @param boolean $batch
     */
    public function setBatch($batch)
    {
        $this->batch = $batch;
    }

    /**
     * Renders rest response.
     *
     * @param mixed $data
     * @param int   $statusCode
     * @param array $headers
     *
     * @return Response|array
     */
    public function renderRest($data, $statusCode = Response::HTTP_OK, $headers = [])
    {
        return $this->isBatch()
            ? ['status_code' => $statusCode, 'response' => $data]
            : $this->get('ongr_api.rest_response_view_handler')->handleView($data, $statusCode, $headers);
    }
}
