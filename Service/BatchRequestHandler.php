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

use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use ONGR\ElasticsearchBundle\Service\Manager;
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Component\HttpFoundation\Request;

/**
 * Service to handle requests and get responses from
 * Elasticsearch in BatchController
 */
class BatchRequestHandler
{
    /**
     * @var Crud
     */
    private $crud;

    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var RequestSerializer
     */
    private $requestSerializer;

    /**
     * @param Crud              $crud
     * @param Manager           $manager
     * @param RequestSerializer $requestSerializer
     */
    public function __construct(
        Crud $crud,
        Manager $manager,
        RequestSerializer $requestSerializer
    ) {
        $this->crud = $crud;
        $this->manager = $manager;
        $this->requestSerializer = $requestSerializer;
    }

    /**
     * Handles the requests and returns the json for the response body
     * @param Request    $request
     * @param Repository $repository
     * @param string     $action
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function handleRequest(Request $request, Repository $repository, $action)
    {
        $commitSize = $this->manager->getBulkCommitSize();
        $documents = $this->requestSerializer->deserializeRequest($request);

        switch ($action) {
            case 'create':
                return $this->create($documents, $repository, $commitSize);
                break;
            case 'update':
                return $this->update($documents, $repository, $commitSize);
                break;
            case 'delete':
                return $this->delete($documents, $repository, $commitSize);
                break;
            default:
                throw new InvalidArgumentException(
                    'handleRequest method can only handle `create`, `update` and `delete` actions.'
                );
        }
    }
    /**
     * Handles create action
     *
     * @param array      $documents
     * @param Repository $repository
     * @param integer    $commitSize
     *
     * @return string
     */
    private function create($documents, $repository, $commitSize)
    {
        if (count($documents) > $commitSize && $commitSize > 1) {
            $esResponse = [];
            $i = 1;
            foreach ($documents as $document) {
                $this->crud->create($repository, $document);
                if ($i++ % ($commitSize - 1) == 0) {
                    $esResponse[] = $this->crud->commit($repository);
                }
            }
        } else {
            foreach ($documents as $document) {
                $this->crud->create($repository, $document);
            }
            $esResponse = $this->crud->commit($repository);
        }
        return json_encode($esResponse);
    }

    /**
     * Handles update action
     *
     * @param array      $documents
     * @param Repository $repository
     * @param integer    $commitSize
     *
     * @return string
     */
    private function update($documents, $repository, $commitSize)
    {
        if (count($documents) > $commitSize && $commitSize > 1) {
            $esResponse = [];
            $i = 1;
            foreach ($documents as $document) {
                $id = $document['_id'];
                unset($document['_id']);
                $this->crud->update($repository, $id, $document);
                if ($i++ % ($commitSize - 1) == 0) {
                    $esResponse[] = $this->crud->commit($repository);
                }
            }
        } else {
            foreach ($documents as $document) {
                $id = $document['_id'];
                unset($document['_id']);
                $this->crud->update($repository, $id, $document);
            }
            $esResponse = $this->crud->commit($repository);
        }
        return json_encode($esResponse);
    }

    /**
     * Handles delete action
     *
     * @param array      $documents
     * @param Repository $repository
     * @param integer    $commitSize
     *
     * @return string
     */
    private function delete($documents, $repository, $commitSize)
    {
        if (count($documents) > $commitSize && $commitSize > 1) {
            $esResponse = [];
            $i = 1;
            foreach ($documents as $document) {
                $this->crud->delete($repository, $document['_id']);
                if ($i++ % ($commitSize - 1) == 0) {
                    $esResponse[] = $this->crud->commit($repository);
                }
            }
        } else {
            foreach ($documents as $document) {
                $this->crud->delete($repository, $document['_id']);
            }
            $esResponse = $this->crud->commit($repository);
        }
        return json_encode($esResponse);
    }
}
