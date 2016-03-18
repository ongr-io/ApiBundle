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

use Elasticsearch\Common\Exceptions\NoDocumentsToGetException;
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CRUD implementation for Api Controller.
 */
class RestController extends Controller implements RestControllerInterface
{

    /**
     * {@inheritdoc}
     */
    public function getAction(Request $request, $documentId)
    {
        $repository = $this->get($request->attributes->get('repository'));
        $requestSerializer = $this->get('ongr_api.request_serializer');

        try {
            $document = $this->get('ongr_api.crud')->read($repository, $documentId);

            if ($document === null) {
                return new Response(
                    $requestSerializer->serializeRequest(
                        $request,
                        [
                            'errors' => [],
                            'message' => 'Document does not exist',
                            'code' => Response::HTTP_NOT_FOUND,
                        ]
                    ),
                    Response::HTTP_NOT_FOUND,
                    ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
                );
            }
        } catch (\Exception $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        return new Response(
            $requestSerializer->serializeRequest($request, $document),
            Response::HTTP_OK,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postAction(Request $request, $documentId = null)
    {
        $crud = $this->get('ongr_api.crud');
        $repository = $this->get($request->attributes->get('repository'));
        $requestSerializer = $this->get('ongr_api.request_serializer');

        $data = $requestSerializer->deserializeRequest($request);

        if (!empty($documentId)) {
            $data['_id'] = $documentId;
        }

        // TODO: validate data

        try {
            $crud->create($repository, $data);
            $response = $crud->commit($repository);
        } catch (\RuntimeException $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_CONFLICT,
                    ]
                ),
                Response::HTTP_CONFLICT,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } catch (\Exception $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        if ($response['errors']) {
            // TODO: 406 validation error
        }

        $documentId = $response['items'][0]['create']['_id'];
        $row = $crud->read($repository, $documentId);
        return new Response(
            $requestSerializer->serializeRequest($request, $row),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function putAction(Request $request, $documentId)
    {
        $crud = $this->get('ongr_api.crud');
        $repository = $this->get($request->attributes->get('repository'));
        $requestSerializer = $this->get('ongr_api.request_serializer');

        $data = $this->get('ongr_api.request_serializer')->deserializeRequest($request);

        // TODO: check validation

        try {
            $crud->update($repository, $documentId, $data);
            $response = $crud->commit($repository);
        } catch (\RuntimeException $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } catch (NoDocumentsToGetException $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } catch (\Exception $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        if ($response['errors']) {
            // TODO: 406 validation error
        }

        $documentId = $response['items'][0]['update']['_id'];
        $row = $crud->read($repository, $documentId);

        return new Response(
            $requestSerializer->serializeRequest($request, $row),
            Response::HTTP_NO_CONTENT,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function deleteAction(Request $request, $documentId)
    {
        $crud = $this->get('ongr_api.crud');
        $repository = $this->get($request->attributes->get('repository'));
        $requestSerializer = $this->get('ongr_api.request_serializer');

        try {
            $crud->delete($repository, $documentId);
            $response = $crud->commit($repository);
        } catch (\RuntimeException $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } catch (NoDocumentsToGetException $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } catch (\Exception $e) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => $e->getMessage(),
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        return new Response(
            $requestSerializer->serializeRequest($request, $response),
            Response::HTTP_NO_CONTENT,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }
}
