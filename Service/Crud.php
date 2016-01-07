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
            throw new \RuntimeException('Document with "' . $id . '" id not found.');
        }
        return $results[0];
    }

    /**
     * {@inheritdoc}
     */
    public function update(Repository $repository, array $data)
    {
        if (!isset($data['_id'])) {
            throw new \RuntimeException('Missing _id field for update operations.');
        }

        if (!$this->read($repository, $data['_id'])) {
            throw new NoDocumentsToGetException("Identifier not found!");
        }

        $repository->getManager()->bulk('update', $repository->getType(), ['_id' => $data['_id'], 'doc' => $data]);
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
}
