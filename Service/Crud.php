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

        $repository->getManager()->bulk('update', $repository->getTypes(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Repository $repository, array $data)
    {
        if (!isset($data['_id'])) {
            throw new \RuntimeException('Missing _id field for delete operations.');
        }

        $repository->getManager()->bulk('delete', $repository->getTypes(), $data);
    }

    /**
     * {@inheritdoc}
     */
    public function commit(Repository $repository)
    {
        return $repository->getManager()->commit();
    }
}
