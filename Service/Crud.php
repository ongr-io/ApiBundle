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

use Elasticsearch\Common\Exceptions\NoDocumentsToGetException;
use ONGR\ElasticsearchBundle\Result\Result;
use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchDSL\Query\IdsQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;

/**
 * Simple CRUD operations service.
 */
class Crud implements CrudInterface
{
    /**
     * {@inheritdoc}
     */
    public function create(Repository $repository, array $data)
    {
        if (!empty($data['_id']) && $this->read($repository, $data['_id'])) {
            throw new \RuntimeException('The resource existed.');
        }

        $repository->getManager()->bulk('create', $repository->getType(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function read(Repository $repository, $id)
    {
        $search = $repository->createSearch();
        $search->addQuery(new IdsQuery([$id]));
        $search->setSize(1);

        $results = $repository->execute($search, Result::RESULTS_ARRAY);

        if (!isset($results[0])) {
            return null;
        }

        return $results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function update(Repository $repository, $id, array $data)
    {
        $repository->getManager()->bulk(
            'update',
            $repository->getType(),
            [
                '_id' => $id,
                'doc' => $data,
                'doc_as_upsert' => true
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Repository $repository, $id)
    {
        if (!$id) {
            throw new \RuntimeException('Missing _id field for update operations.');
        }

        if (!$this->read($repository, $id)) {
            throw new NoDocumentsToGetException("Identifier not found!");
        }

        $repository->getManager()->bulk('delete', $repository->getType(), ['_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(Repository $repository)
    {
        return $repository->getManager()->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function readAll(Repository $repository, array $parameters = [])
    {
        $search = $repository->createSearch();
        $search->addQuery(new MatchAllQuery());

        if (isset($parameters['size'])) {
            $search->setSize($parameters['size']);
        }
        if (isset($parameters['from'])) {
            $search->setFrom($parameters['from']);
        }

        $results = $repository->execute($search, Result::RESULTS_ARRAY);

        if (!isset($results[0])) {
            return null;
        }

        return $results;
    }
}
