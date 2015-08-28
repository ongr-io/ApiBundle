<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Controller;

use Elasticsearch\Common\Exceptions\ElasticsearchException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Rest controller for handling command actions.
 */
class CommandController extends AbstractRestController implements CommandControllerInterface
{
    /**
     * {@inheritdoc}
     */
    public function createIndexAction(Request $request, $manager)
    {
        try {
            $this->get($manager)->getConnection()->createIndex();
        } catch (ElasticsearchException $e) {
            return $this->createExceptionResponse($e);
        }

        return $this->renderRest(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function dropIndexAction(Request $request, $manager)
    {
        try {
            $this->get($manager)->getConnection()->dropIndex();
        } catch (ElasticsearchException $e) {
            return $this->createExceptionResponse($e);
        }

        return $this->renderRest(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSchemaAction(Request $request, $manager)
    {
        try {
            $data = ['status' => $this->get($manager)->getConnection()->updateTypes()];
        } catch (ElasticsearchException $e) {
            return $this->createExceptionResponse($e);
        }

        switch ($data['status']) {
            case 1:
                $data['message'] = 'Mapping has been successfully updated';
                break;
            case 0:
                $data['message'] = 'Mapping is already up to date';
                break;
            default:
                $data['message'] = 'No mapping was found';
                break;
        }

        return $this->renderRest($data, Response::HTTP_OK);
    }

    /**
     * Creates elasticsearch exception response.
     *
     * @param ElasticsearchException $exception
     *
     * @return array|Response
     */
    protected function createExceptionResponse(ElasticsearchException $exception)
    {
        return $this->renderRest(
            [
                'message' => 'Elasticsearch client exception',
                'error' => $exception->getMessage(),
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
