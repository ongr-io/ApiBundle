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
        $mappingCollections = $this->container->get('ongr_api.command_controller')
            ->getMapping();

        foreach ($versions as $version => $config) {

            $path = '';
            if ($this->container->getParameter('ongr_api.version_in_url')) {
                $path .= '/' . $version;
            }

            foreach ($config['endpoints'] as $document => $endpoint) {
                $this->processRestRequest($document, $endpoint, $path, $version);
                $this->processCommandRequest($document, $endpoint, $mappingCollections, $path, $version);
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
        $defaults = [
            'id' => null,
            '_endpoint' => $endpoint,
            '_version' => $version
        ];
        $requirements = [];

        foreach ($endpoint['methods'] as $method) {
            $name = strtolower(sprintf('ongr_api_%s_%s_%s', $version, $document, $method));
            $defaults['_controller'] = sprintf('ONGRApiBundle:Rest:%s', strtolower($method));

            $this->add($name, new Route($pattern, $defaults, $requirements, [], "", [], [$method]));
        }
    }

    /**
     * Add Route Configuration of RESTful command request
     *
     * @param string $document
     * @param array $endpoint
     * @param array $mapping
     * @param string $path
     * @param string $version
     */
    private function processCommandRequest(
        $document,
        $endpoint,
        $mapping,
        $path = '',
        $version = 'v1'
    ) {

        foreach ($mapping as $command => $config) {
            if (isset($endpoint[$config['validator']]) && $endpoint[$config['validator']]) {

                $pattern = strtolower(sprintf('%s/%s/%s', $path, $document, $command));
                $name = strtolower(
                    sprintf('ongr_api_%s_%s_%s', $version, $document, $command)
                );
                $defaults = [
                    '_endpoint' => $endpoint,
                    '_version' => $version,
                    '_controller' => $config['_controller']
                ];
                $methods = $config['methods'];

                $this->add($name, new Route($pattern, $defaults, [], [], "", [], $methods));
            }
        }
    }
}
