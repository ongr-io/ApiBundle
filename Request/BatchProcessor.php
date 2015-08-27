<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Request;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * Handles multiple actions on one request.
 */
class BatchProcessor implements ContainerAwareInterface
{
    /**
     * @var OptionsResolver
     */
    private $resolver;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->resolver = new OptionsResolver();
        $this->configureResolver($this->resolver);
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

        $proxy = RestRequestProxy::initialize($restRequest);
        $out = [];
        $currentMethod = $this->getRouter()->getContext()->getMethod();

        foreach ($data as $action) {
            $action = $this->resolver->resolve($action);
            $this->getRouter()->getContext()->setMethod($action['method']);
            try {
                $options = $this->getRouter()->match('/api/' . $restRequest->getVersion() . '/' . $action['path']);
            } catch (ResourceNotFoundException $e) {
                $out[] = [
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Could not resolve path!',
                    'error' => $e->getMessage(),
                ];
                continue;
            }
            list($id, $method) = explode(':', $options['_controller'], 2);
            $controller = $this->getContainer()->get($id);
            $controller->setBatch(true);

            $proxy->setData($action['body']);
            $proxy->setRepository(
                $this
                    ->getContainer()
                    ->get($options['manager'])
                    ->getRepository($options['repository'])
            );

            $out[] = call_user_func_array([$controller, $method], [$proxy, $options['id']]);
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
     * Options resolver setup.
     *
     * @param OptionsResolver $resolver
     */
    protected function configureResolver(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['method', 'path', 'body'])
            ->setAllowedTypes(
                [
                    'method' => 'string',
                    'path' => 'string',
                    'body' => 'array',
                ]
            )
            ->setNormalizer(
                'method',
                function ($options, $value) {
                    return strtoupper($value);
                }
            );
    }
}
