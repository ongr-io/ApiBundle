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

interface CrudInterface
{
    /**
     * Document create action.
     *
     * @param Repository $repository
     * @param array      $data
     */
    public function create(Repository $repository, array $data);

    /**
     * Document fetch by id action. Uses Elastcisearch get API.
     *
     * @param Repository $repository
     * @param string     $id
     *
     * @return array
     */
    public function read(Repository $repository, $id);

    /**
     * Update document action.
     *
     * @param Repository $repository
     * @param array      $data
     *
     * @throws \RuntimeException
     */
    public function update(Repository $repository, array $data);

    /**
     * Delete action.
     *
     * @param Repository $repository
     * @param array      $data
     */
    public function delete(Repository $repository, array $data);

    /**
     * Commit changes to the elasticsearch.
     *
     * @param Repository $repository
     *
     * @return array
     */
    public function commit(Repository $repository);
}