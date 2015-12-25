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
     * @var string
     */
    private $defaultAcceptType;

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
        $this->setRequest($request !== null ? $request : Request::createFromGlobals());


        if ($this->getRequest()->attributes->has('manager') && $this->getRequest()->attributes->has('repository')) {
            $attributes = $this->getRequest()->attributes;
            $manager = $this->container->get($attributes->get('manager'));

            /** @var Repository $repository */
            $repository = $manager->getRepository($attributes->get('repository'));
            $this->setRepository($repository);
        }

        $this->setDefaultAcceptType($this->container->getParameter('ongr_api.default_encoding'));
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
     * Fetches deserialized request content.
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
     * @param \Symfony\Component\HttpFoundation\Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return string
     *
     * @throws \RuntimeException
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
        return $this->getSerializer()
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
            return $this->getSerializer()
                ->deserialize($data, 'array', $this->checkAcceptHeader());
        } catch (\Exception $e) {
            // Do nothing, returns null.
        }

        return null;
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

    /**
     * Used for validator to check if it can ignore unknown fields.
     *
     * @return bool
     */
    public function isAllowedExtraFields()
    {
        return $this->getRequest()->attributes->get('_allow_extra_fields', false);
    }

    /**
     * Returns using repository type.
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->getRequest()->attributes->get('_type');
    }
}
