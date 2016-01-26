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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApiRouteCollection extends RouteCollection
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Populates endpoints into route collection.
     */
    public function collectRoutes()
    {
        $versions = $this->container->getParameter('ongr_api.versions');

        foreach ($versions as $version => $config) {

            $path = '';
            if ($this->container->getParameter('ongr_api.version_in_url')) {
                $path .= '/' . $version;
            }

            foreach ($config['endpoints'] as $document => $endpoint) {
                $this->processRestRequest($document, $endpoint, $path, $version);
            }
        }
    }

    /**
     * Add Route Configuration of RESTful request
     *
     * @param string $document
     * @param array $endpoint
     * @param string $path
     * @param string $version
     */
    private function processRestRequest(
        $document,
        $endpoint,
        $path = '',
        $version = 'v1'
    ) {
        $pattern = strtolower(sprintf('%s/%s/{id}', $path, $document));
        $variantPattern = strtolower("$path/$document") . '/{documentId}/_variant/{id}';
        $defaults = [
            'id' => null,
            '_endpoint' => $endpoint,
            '_version' => $version,
            'repository' => $endpoint['repository'],
        ];
        $requirements = [];

        foreach ($endpoint['methods'] as $method) {
            $name = strtolower(sprintf('ongr_api_%s_%s_%s', $version, $document, $method));
            $defaults['_controller'] = sprintf('ONGRApiBundle:Rest:%s', strtolower($method));

            $this->add($name, new Route($pattern, $defaults, $requirements, [], "", [], [$method]));

            if ($endpoint['variants']) {
                $variantDefaults = array_merge(
                    $defaults,
                    ['_controller' => sprintf('ONGRApiBundle:Variant:%s', strtolower($method))]
                );

                $this->add(
                    $name . '_variant',
                    new Route($variantPattern, $variantDefaults, $requirements, [], "", [], [$method])
                );
            }
        }
    }
}
