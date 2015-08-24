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

use ONGR\ApiBundle\Request\RestRequestProxy;
use Symfony\Component\HttpFoundation\Response;

class ViewHandler
{
    /**
     * @var RestRequestProxy
     */
    private $requestProxy;

    /**
     * @param RestRequestProxy $restRequestProxy
     */
    public function __construct(RestRequestProxy $restRequestProxy)
    {
        $this->requestProxy = $restRequestProxy;
    }

    /**
     * Creates response for rest to return.
     *
     * @param mixed $data
     * @param int   $statusCode
     *
     * @return Response
     */
    public function handleView($data, $statusCode = Response::HTTP_OK)
    {
        return new Response(
            $this->getRequestProxy()->serialize($data),
            $statusCode,
            ['Content-Type' => 'application/' . $this->getRequestProxy()->checkAcceptHeader()]
        );
    }

    /**
     * @return RestRequestProxy
     */
    protected function getRequestProxy()
    {
        return $this->requestProxy;
    }
}
