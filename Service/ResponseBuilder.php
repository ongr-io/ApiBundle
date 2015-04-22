<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds Response according to given data and parameters.
 */
class ResponseBuilder
{
    /**
     * Creates response for get requests.
     *
     * @param Request $request
     * @param array   $data
     *
     * @return Response
     */
    public function getResponse(Request $request, $data)
    {
        $format = $this->getResponseFormat($request);
        $mime = $this->getResponseMime($format);

        $data = $this->encodeArray($data, $format);

        $response = new Response();
        $response->setContent($data);
        $response->headers->set('Content-Type', $mime);

        return $response;
    }

    /**
     * Encodes array to given format.
     *
     * @param array  $data
     * @param string $format
     *
     * @return mixed
     * @throws \DomainException
     */
    private function encodeArray($data, $format)
    {
        switch ($format) {
            case 'json':
                return json_encode($data);
            default:
                throw new \DomainException("Unknown format \"{$format}\"");
        }
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    private function getResponseFormat(Request $request)
    {
        return 'json';
    }

    /**
     * @param string $format
     *
     * @return string
     *
     * @throws \DomainException
     */
    private function getResponseMime($format)
    {
        static $types = [
            'json' => 'application/json',
        ];

        if (!isset($types[$format])) {
            throw new \DomainException("Unknown format \"{$format}\"");
        }

        return $types[$format];
    }
}
