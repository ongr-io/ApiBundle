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
use ONGR\ElasticsearchBundle\ORM\Repository;
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
     * @var string
     */
    private $defaultAcceptType;

    /**
     * @param Request             $request
     * @param SerializerInterface $serializer
     * @param Repository          $repository
     */
    public function __construct(Request $request, SerializerInterface $serializer, Repository $repository = null)
    {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->repository = $repository;
    }

    /**
     * Proxy call method to original request.
     *
     * @param string $name
     * @param array  $arguments
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
     * Proxy get metod for original properties.
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
     * @return Repository|null
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Fethes deserialized request content.
     *
     * @return array
     */
    public function getData()
    {
        return $this->deserialize($this->getRequest()->getContent());
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return string
     *
     * @throws RuntimeException
     */
    public function getDefaultAcceptType()
    {
        if (!$this->defaultAcceptType) {
            throw new \RuntimeException('Please set acceptable content type to request or set default accept type.');
        }

        return $this->defaultAcceptType;
    }

    /**
     * @param string $defaultAcceptType
     */
    public function setDefaultAcceptType($defaultAcceptType)
    {
        $this->defaultAcceptType = $defaultAcceptType;
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
        return $this
            ->getSerializer()
            ->serialize($data, $this->checkAcceptHeader());
    }

    /**
     * Deserializes content.
     *
     * @param mixed $data
     *
     * @return array|null
     */
    public function deserialize($data)
    {
        try {
            return $this
                ->getSerializer()
                ->deserialize($data, 'array', $this->checkAcceptHeader());
        } catch (\Exception $e) {
            // Do nothing, returns null.
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
     * Returns accpetance type based on given request.
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
     * Returns api version.
     *
     * @return string|null
     */
    public function getVersion()
    {
        return $this->getRequest()->attributes->get('_version');
    }
}
