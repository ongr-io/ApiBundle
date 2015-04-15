<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Event;

use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PreGetEvent.
 */
class PreGetEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Search
     */
    private $search;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param Request    $request
     * @param Search     $search
     * @param Repository $repository
     */
    public function __construct(Request $request, Search $search, Repository $repository)
    {
        $this->search = $search;
        $this->request = $request;
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
     * @return Search
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return Repository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
