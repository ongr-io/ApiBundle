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

use ONGR\ApiBundle\Controller\RestControllerInterface;
use ONGR\ApiBundle\Request\RestRequest;
use ONGR\ApiBundle\Request\RestRequestProxy;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Handles multiple actions on one request.
 */
class BatchProcessor implements ContainerAwareInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var CrudInterface
     */
    private $crud;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return CrudInterface
     */
    public function getCrud()
    {
        return $this->crud;
    }

    /**
     * @param CrudInterface $crud
     */
    public function setCrud(CrudInterface $crud)
    {
        $this->crud = $crud;
    }

    /**
     * Handles batch process.
     *
     * @param RestRequest $restRequest

     * @return array|bool Returns false on deserialization error.
     */
    public function handle(RestRequest $restRequest)
    {
        $data = $restRequest->getData();

        if ($data === null) {
            return false;
        }

        $output = [];
        $indexes = [];

        foreach ($data as $batch) {

//            $method = $batch['method'];
//            switch ($method) {
//
//                case Request::METHOD_POST:
//                    $output[]
//                    break;
//            }

            $this->getRouter()->getContext()->setMethod($action['method']);
            try {
                $options = $this->getRouter()->match('/api/' . $restRequest->getVersion() . '/' . $action['path']);
            } catch (ResourceNotFoundException $e) {
                $out[] = [
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Could not resolve path or action!',
                    'error' => $e->getMessage(),
                ];
                continue;
            }
            list($id, $method) = explode(':', $options['_controller'], 2);
            $this->prepareProxy($proxy, $action['body'], $options);


            $out[] = call_user_func_array([$this->getController($id), $method], [$proxy, $options['id']]);
            $indexes[$proxy->getRepository()->getManager()->getIndexName()] = null;
        }

        if (!empty($indexes)) {
            $proxy
                ->getRepository()
                ->getManager()
                ->getClient()
                ->indices()
                ->flush(['index' => implode(',', array_keys($indexes))]);
        }
        $this->getRouter()->getContext()->setMethod($currentMethod);

        return $out;
    }

    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * Sets data into proxy.
     *
     * @param RestRequestProxy $proxy
     * @param mixed            $body
     * @param array            $options
     */
    private function prepareProxy(RestRequestProxy $proxy, $body, $options)
    {
        $proxy
            ->setData($body)
            ->setRepository(
                $this
                    ->getContainer()
                    ->get($options['manager'])
                    ->getRepository($options['repository'])
            )
            ->setType($options['_type'])
            ->setAllowedExtraFields($options['_allow_extra_fields']);
    }

    /**
     * Retrieves controller from container and sets batch flag.
     *
     * @param string $id Service id.
     *
     * @return RestControllerInterface
     */
    private function getController($id)
    {
        $controller = $this->getContainer()->get($id);
        if (method_exists($controller, 'setBatch')) {
            $controller->setBatch(true);
        }

        return $controller;
    }
}
