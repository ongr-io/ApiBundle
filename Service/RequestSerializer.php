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

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This class provides all data for deserialization and serialization of API requests.
 */
class RequestSerializer
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $defaultAcceptType;

    /**
     * RequestSerializer constructor.
     *
     * @param SerializerInterface $serializer
     * @param string              $defaultAcceptType
     */
    public function __construct(SerializerInterface $serializer, $defaultAcceptType)
    {
        $this->serializer = $serializer;
        $this->defaultAcceptType = $defaultAcceptType;
    }

    /**
     * @param Request $request
     * @param string  $data
     *
     * @return string
     */
    public function serializeRequest(Request $request, $data)
    {
        $format = $this->checkAcceptHeader($request);

        try {
            return $this->serializer->serialize($data, $format);
        } catch (\Exception $e) {
            throw new \RuntimeException('Could not serialize content to \'' . $format .'\' format.');
        }
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function deserializeRequest(Request $request)
    {
        $type = 'array';
        $format = $this->checkAcceptHeader($request);

        try {
            return $this->serializer->deserialize($request->getContent(), $type, $format);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                'Could not deserialize content to object of \'' . $type . '\' type and \'' . $format . '\' format.'
            );
        }
    }

    /**
     * Returns acceptance type based on given request.
     *
     * @return string
     */
    public function checkAcceptHeader(Request $request)
    {
        $headers = $request->getAcceptableContentTypes();

        if (array_intersect($headers, ['application/json', 'text/json'])) {
            return 'json';
        } elseif (array_intersect($headers, ['application/xml', 'text/xml'])) {
            return 'xml';
        }

        return $this->defaultAcceptType;
    }
}
