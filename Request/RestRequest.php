<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Request;

use JMS\Serializer\SerializerInterface;
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Object for representing rest request.
 */
class RestRequest
{

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        /** @var SerializerInterface $serializer */
        $serializer = $this->container->get('serializer');
        $this->setSerializer($serializer);

        /** @var Request $request */
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $this->setRequest(($request !== null ? $request : Request::createFromGlobals()));

        /** @var Repository $repository */
        $repository = $this->container->get($this->getRequest()->attributes->get('_endpoint')['repository']);
        $this->setRepository($repository);
    }

    /**
     * Proxy call method to original request.
     *
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->getRequest(), $name)) {
            return call_user_func_array([$this->getRequest(), $name], $arguments);
        }

        throw new \BadMethodCallException(sprintf("'%s' method does not exist!", $name));
    }

    /**
     * Proxy get method for original properties.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getRequest()->{$name};
    }

    /**
     * Fetches de-serialized request content.
     *
     * @return array
     */
    public function getData()
    {
        try {
            $response = $this->deserialize($this->getRequest()->getContent());
        } catch (\Exception $e) {
            $response = [];
        }

        return $response;
    }

    /**
     * Returns api version.
     *
     * @return string|null
     */
    public function getVersion()
    {
        return $this->getRequest()->attributes->get('_version', 'v1');
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
     */
    public function getDefaultAcceptType()
    {
        $defaultAcceptType = $this->container->getParameter('ongr_api.default_encoding');

        if (!$defaultAcceptType) {
            throw new \RuntimeException('Please set acceptable content type to request or set default accept type.');
        }

        return $defaultAcceptType;
    }

    /**
     * @return Repository|null
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }


    /**
     * Encodes data for response.
     *
     * @param array $data
     *
     * @return string
     */
    public function serialize($data)
    {
        return $this->getSerializer()
            ->serialize($data, $this->checkAcceptHeader());
    }

    /**
     * Deserialize content.
     *
     * @param mixed $data
     * @return array|null
     * @throws \RuntimeException
     */
    public function deserialize($data)
    {
        $type = 'array';
        $format = $this->checkAcceptHeader();

        try {
            return $this->getSerializer()
                ->deserialize($data, $type, $format);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf(
                    'Could not deserialize request content to object of \'%s\' type and \'%s\' format',
                    $type,
                    $format
                )
            );
        }
    }

    /**
     * @return SerializerInterface
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    /**
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Returns acceptance type based on given request.
     *
     * @return string
     */
    public function checkAcceptHeader()
    {
        $headers = $this->getRequest()->getAcceptableContentTypes();

        if (array_intersect($headers, ['application/xml', 'text/xml'])) {
            return 'xml';
        } elseif (array_intersect($headers, ['application/json', 'text/json'])) {
            return 'json';
        }

        return $this->getDefaultAcceptType();
    }

    /**
     * Used for validator to check if it can ignore unknown fields.
     *
     * @return bool
     */
    public function isAllowedExtraFields()
    {
        return $this->getRequest()->attributes->get(
            '_endpoint',
            [
                'allow_extra_fields' => true
            ]
        )['allow_extra_fields'];
    }

    /**
     * if this option is set, API will allow only to operate with specified fields from the type.
     *
     * @return String|Array
     */
    public function getAllowedFields()
    {
        return $this->getRequest()->attributes->get(
            '_endpoint',
            [
                'allow_fields' => "~"
            ]
        )['allow_fields'];
    }

    /**
     * allows to get all values.
     *
     * @return boolean
     */
    public function isAllowedGetAll()
    {
        return $this->getRequest()->attributes->get(
            '_endpoint',
            [
                'allow_get_all' => true
            ]
        )['allow_get_all'];
    }

    /**
     * You can sent then an array of documents to be indexed to the particular endpoint type.
     *
     * @return boolean
     */
    public function isAllowedBatch()
    {
        return $this->getRequest()->attributes->get(
            '_endpoint',
            [
                'allow_batch' => true
            ]
        )['allow_batch'];
    }
}
