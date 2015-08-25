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
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
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
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
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
