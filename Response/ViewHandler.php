<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Response;

use ONGR\ApiBundle\Request\RestRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * View handler for displaying in correct content type.
 */
class ViewHandler
{
    /**
     * @var RestRequest
     */
    private $restRequest;

    /**
     * @param RestRequest $restRequest
     */
    public function __construct(RestRequest $restRequest)
    {
        $this->restRequest = $restRequest;
    }

    /**
     * Creates response for rest to return.
     *
     * @param mixed $data
     * @param int   $statusCode
     * @param array $headers
     *
     * @return Response
     */
    public function handleView($data, $statusCode = Response::HTTP_OK, $headers = [])
    {
        $contentType = $this->getRestRequest()->checkAcceptHeader();
        if ($contentType == 'json' && null !== $this->getRestRequest()->get('pretty')) {
            $dataResponse = json_encode($data, JSON_PRETTY_PRINT);
        } else {
            $dataResponse = $this->getRestRequest()->serialize($data);
        }

        return new Response(
            $dataResponse,
            $statusCode,
            array_merge(
                ['Content-Type' => 'application/' . $this->getRestRequest()->checkAcceptHeader()],
                $headers
            )
        );
    }

    /**
     * @return RestRequest
     */
    public function getRestRequest()
    {
        return $this->restRequest;
    }
}
