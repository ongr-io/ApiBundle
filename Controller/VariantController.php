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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * This controller works with document variants.
 */
class VariantController extends Controller
{
    /**
     * @inheritDoc
     */
    public function getAction(Request $request, $documentId, $variantId = null)
    {
        $crud = $this->get('ongr_api.crud');
        $requestSerializer = $this->get('ongr_api.request_serializer');

        try {
            $document = $crud->read($this->get($request->attributes->get('repository')), $documentId);
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

        if (!isset($document['variants'])) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'Document does not support variants.',
                        'code' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    ]
                ),
                Response::HTTP_INTERNAL_SERVER_ERROR,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        if ($variantId === null) {
            return new Response(
                $requestSerializer->serializeRequest($request, $document['variants']),
                Response::HTTP_OK,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } elseif (isset($document['variants'][$variantId])) {
            return new Response(
                $requestSerializer->serializeRequest($request, $document['variants'][$variantId]),
                Response::HTTP_OK,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        } else {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'Variant "' . $variantId . '" for object "' . $documentId . '" does not exist.',
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function postAction(Request $request, $documentId)
    {
        $crud = $this->get('ongr_api.crud');
        $repository = $this->get($request->attributes->get('repository'));
        $requestSerializer = $this->get('ongr_api.request_serializer');

        $document = $crud->read($repository, $documentId);

        if (!$document) {
            return $this->renderError($request, 'Document was not found', Response::HTTP_NOT_FOUND);
        }

        $document['variants'] = $this->get('ongr_api.request_serializer')->deserializeRequest($request);

        try {
            $crud->update($repository, $documentId, $document);
            $crud->commit($repository);
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

        $row = $crud->read($repository, $documentId);
        return new Response(
            $requestSerializer->serializeRequest($request, $row),
            Response::HTTP_CREATED,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }

    /**
     * @inheritDoc
     */
    public function putAction(Request $request, $documentId, $variantId)
    {
        $requestSerializer = $this->get('ongr_api.request_serializer');

        if ($variantId === null) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'You must provide variant id in your request.',
                        'code' => Response::HTTP_BAD_REQUEST,
                    ]
                ),
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        $crud = $this->get('ongr_api.crud');
        $repository = $this->get($request->attributes->get('repository'));

        $document = $crud->read($repository, $documentId);

        if (!$document) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'Document was not found',
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        if (!isset($document['variants'][$variantId])) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'Variant "' . $variantId . '" for object "' . $documentId . '" does not exist.',
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        $document['variants'][$variantId] = $requestSerializer->deserializeRequest($request);

        $crud->update($repository, $documentId, $document);
        $crud->commit($repository);

        return new Response(
            $requestSerializer->serializeRequest($request, $document),
            Response::HTTP_OK,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteAction(Request $request, $documentId, $variantId = null)
    {
        $crud = $this->get('ongr_api.crud');
        $requestSerializer = $this->get('ongr_api.request_serializer');
        $repository = $this->get($request->attributes->get('repository'));

        $document = $crud->read($repository, $documentId);

        if (!$document) {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'Document was not found',
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        if ($variantId === null) {
            $document['variants'] = [];
            $crud->update($repository, $documentId, $document);
        } elseif (isset($document['variants'][$variantId])) {
            unset($document['variants'][$variantId]);
            $document['variants'] = array_values($document['variants']);

            $crud->update($repository, $documentId, $document);
        } else {
            return new Response(
                $requestSerializer->serializeRequest(
                    $request,
                    [
                        'errors' => [],
                        'message' => 'Variant "' . $variantId . '" for object "' . $documentId . '" does not exist.',
                        'code' => Response::HTTP_NOT_FOUND,
                    ]
                ),
                Response::HTTP_NOT_FOUND,
                ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
            );
        }

        $crud->commit($repository);

        return new Response(
            $requestSerializer->serializeRequest($request, $document),
            Response::HTTP_OK,
            ['Content-Type' => 'application/' . $requestSerializer->checkAcceptHeader($request)]
        );
    }
}
