<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads api routes.
 */
class ElasticsearchLoader extends Loader
{
    /**
     * @var array
     */
    private $versions;

    /**
     * @param array $versions
     */
    public function __construct(array $versions = [])
    {
        $this->versions = $versions;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $collection = new RouteCollection();

        foreach ($this->versions as $version => $config) {
            foreach ($config['endpoints'] as $document => $endpoint) {
                $this->processRestRoute($collection, $document, $endpoint, $version);
            }
        }

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'ongr_api' === $type;
    }

    /**
     * Create route for REST action
     *
     * @param RouteCollection $collection
     * @param string $document
     * @param array $endpoint
     * @param string $version
     */
    private function processRestRoute(
        $collection,
        $document,
        $endpoint,
        $version = 'v1'
    ) {
    
        $defaults = [
            '_documentId' => null,
            '_endpoint' => $endpoint,
            '_version' => $version,
            'repository' => $endpoint['repository'],
        ];

        $pattern = $version . '/' . sprintf('%s/{documentId}', strtolower($document));

        if ($endpoint['batch']) {
            $defaults['_controller'] = 'ONGRApiBundle:Batch:Process';
            $batchPattern = $version . '/' . sprintf('%s', strtolower($document)) . '/_batch';
            $name = strtolower(sprintf('ongr_api_%s_%s_%s', $version, $document, Request::METHOD_POST));
            $collection->add($name . '_batch', new Route(
                $batchPattern,
                $defaults,
                [],
                [],
                "",
                [],
                [Request::METHOD_POST]
            ));
        }

        foreach ($endpoint['methods'] as $method) {
            $name = strtolower(sprintf('ongr_api_%s_%s_%s', $version, $document, $method));
            $defaults['_controller'] = sprintf('ONGRApiBundle:Rest:%s', strtolower($method));

            if ($method == Request::METHOD_POST) {
                $postPattern = $version . '/' . sprintf('%s', strtolower($document));
                $collection->add($name . '_wi', new Route($postPattern, $defaults, [], [], "", [], [$method]));
            }

            $collection->add($name, new Route($pattern, $defaults, [], [], "", [], [$method]));

            if ($endpoint['variants']) {
                $defaults['_controller'] = sprintf('ONGRApiBundle:Variant:%s', strtolower($method));

                if ($method == Request::METHOD_POST || $method == Request::METHOD_GET) {
                    $variantPattern = $pattern . '/_variant';
                    $collection->add(
                        $name . '_variant_wi',
                        new Route($variantPattern, $defaults, [], [], "", [], [$method])
                    );
                }

                $variantPattern = $pattern . '/_variant/{variantId}';
                $collection->add($name . '_variant', new Route($variantPattern, $defaults, [], [], "", [], [$method]));
            }
        }
    }
}
