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

use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\DependencyInjection\Container;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\DSL\Search;

/**
 * Class DataRequestService, for managing request to a Document repository.
 */
class DataRequestService
{
    /**
     * @var Manager Data Documents manager.
     */
    protected $dataManager;

    /**
     * @var Repository Data document's repository.
     */
    protected $dataRepository;

    /**
     * @param Container $container
     * @param string    $manager
     * @param string    $document
     * @param array     $fields
     */
    public function __construct(
        Container $container,
        $manager,
        $document,
        $fields
    ) {
        $this->dataManager = $container->get($manager);

        $this->dataRepository = $this->dataManager->getRepository($document);
    }

    /**
     * Repository getter.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function get($params)
    {
        /** @var Search $search */
        $search = $this->dataRepository->createSearch();

        $query = new MatchAllQuery();

        $search->addQuery($query);

        return $this->dataRepository->execute($search);
    }
}
