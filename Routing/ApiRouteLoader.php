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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads api routes.
 */
class ApiRouteLoader implements LoaderInterface
{
    /**
     * @var array
     *
     * Request types supported by API.
     */
    public static $supportedTypes = [
        'PUT',
        'GET',
        'POST',
        'DELETE',
    ];

    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "ApiRoute" loader twice');
        }

        $routes = new RouteCollection();

        $versions = $this->container->getParameter('ongr_api.versions');

        foreach ($versions as $version) {
            $endpoints = $this->container->getParameter('ongr_api.' . $version . '.endpoints');
            foreach ($endpoints as $endpoint) {
                $path = '/' . $version . '/' . $endpoint;

                $controller = $this->container->getParameter("ongr_api.$version.$endpoint.controller");
                foreach (self::$supportedTypes as $type) {
                    if ($controller['name'] != 'default') {
                        $route = $this->createCustomRoute(
                            $path,
                            $type,
                            $controller,
                            $this->container->getParameter("ongr_api.$version.$endpoint")
                        );
                    } else {
                        $method = "ONGRApiBundle:Api:$type";
                        $service = "ongr_api.service.$version.$endpoint.data_request";
                        $defaults = [
                            '_controller' => $method,
                            'endpoint' => $service,
                        ];

                        $route = new Route(
                            $path,
                            $defaults,
                            [],
                            [],
                            '',
                            [],
                            [$type],
                            ''
                        );
                    }
                    $name = 'ongr_api_' . $version . '_' . $endpoint . '_' . $type;
                    $routes->add($name, $route);
                }
            }
        }

        $this->loaded = true;

        return $routes;
    }

    /**
     * Creates route for custom controller.
     *
     * @param string $path
     * @param string $type
     * @param array  $controller
     * @param array  $endpoint
     *
     * @return Route
     */
    private function createCustomRoute($path, $type, $controller, $endpoint)
    {
        $method = "{$controller['name']}:{$type}";
        $defaults = [
            '_controller' => $method,
            'endpoint' => $endpoint,
        ];

        if (isset($controller['defaults'])) {
            $defaults = array_merge($controller['defaults'], $defaults);
        }

        if (isset($controller['path'])) {
            $path .= $controller['path'];
        }

        if (isset($controller['requirements'])) {
            $requirements = $controller['requirements'];
        } else {
            $requirements = [];
        }

        if (isset($controller['options'])) {
            $options = $controller['options'];
        } else {
            $options = [];
        }

        return new Route(
            $path,
            $defaults,
            $requirements,
            $options,
            '',
            [],
            [$type],
            ''
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'apiroute' === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }
}
