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
use ONGR\ElasticsearchBundle\Service\Repository;

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

        $repository->getManager()->bulk('create', $repository->getTypes(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function read(Repository $repository, $id)
    {
        return $repository->find($id, Repository::RESULTS_ARRAY);
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

        $repository->getManager()->bulk('update', $repository->getTypes(), ['_id' => $data['_id'], 'doc' => $data]);
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

        $repository->getManager()->bulk('delete', $repository->getTypes(), ['_id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(Repository $repository)
    {
        return $repository->getManager()->commit();
    }
}
