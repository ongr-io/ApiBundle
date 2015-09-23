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

use ONGR\ElasticsearchBundle\ORM\Repository;

/**
 * Rest request proxy used in batch requests.
 */
class RestRequestProxy extends RestRequest
{
    /**
     * @var array
     */
    private $data;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var bool
     */
    private $allowedExtraFields = false;

    /**
     * @var string
     */
    private $type;

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Repository $repository
     *
     * @return $this
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isAllowedExtraFields()
    {
        return $this->allowedExtraFields;
    }

    /**
     * @param bool $allowedExtraFields
     *
     * @return $this
     */
    public function setAllowedExtraFields($allowedExtraFields)
    {
        $this->allowedExtraFields = $allowedExtraFields;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Initializes proxy rest request.
     *
     * @param RestRequest $restRequest
     *
     * @return RestRequestProxy
     */
    public static function initialize(RestRequest $restRequest)
    {
        return new self($restRequest->getRequest(), $restRequest->getSerializer());
    }
}
