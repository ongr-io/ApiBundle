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

/**
 * Abstraction for rest api controller.
 */
class AbstractRestController extends Controller
{
    /**
     * @var bool
     */
    private $batch = false;

    /**
     * @return bool
     */
    public function isBatch()
    {
        return $this->batch;
    }

    /**
     * @param bool $batch
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
        if ($this->isBatch()) {
            return ['status_code' => $statusCode, 'response' => $data];
        }

        return $this->get('ongr_api.rest_response_view_handler')->handleView($data, $statusCode, $headers);
    }
}
