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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractApiController extends Controller implements ApiControllerInterface
{
    /**
     * Creates response for rest to return.
     *
     * @param Request $request
     * @param array   $data
     * @param int     $statusCode
     *
     * @return Response
     */
    protected function createResponse(Request $request, $data, $statusCode = Response::HTTP_OK)
    {
        $accept = $this->checkAcceptHeader($request);

        return new Response(
            $this->serialize($data, $accept),
            $statusCode,
            ['Content-Type' => 'application/' . $accept]
        );
    }

    /**
     * Encodes data for response.
     *
     * @param array $data
     * @param string $format
     *
     * @return string
     */
    protected function serialize($data, $format)
    {
        return $this
            ->container
            ->get('jms_serializer')
            ->serialize($data, $format);
    }

    /**
     * Deserializes content.
     *
     * @param string $data
     * @param string $format
     *
     * @return array
     */
    protected function deserialize($data, $format)
    {
        return $this
            ->container
            ->get('jms_serializer')
            ->deserialize($data, 'array', $format);
    }


    /**
     * Returns accpetance type based on given request.
     *
     * @param Request $request
     *
     * @return string
     */
    private function checkAcceptHeader(Request $request)
    {
        $headers = $request->getAcceptableContentTypes();

        if (array_intersect($headers, ['application/xml', 'text/xml'])) {
            return 'xml';
        } elseif (array_intersect($headers, ['application/json', 'text/json'])) {
            return 'json';
        }

        return $this->container->getParameter('ongr_api.default_encoding');
    }
}
