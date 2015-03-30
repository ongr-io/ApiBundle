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
 * Class for loading api routes.
 *
 * Class ApiRouteLoader
 *
 * @package Acme\DemoBundle\Routing
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
                    if ($controller == 'default') {
                        $method = "ONGRApiBundle:Api:$type";
                        $service = "ongr_api.service.$version.$endpoint.data_request";
                        $defaults = [
                            '_controller' => $method,
                            'endpoint' => $service,
                        ];
                    } else {
                        $method = "$controller:$type";
                        $defaults = [
                            '_controller' => $method,
                        ];
                    }
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
                    $name = 'ongr_api_' . $version . '_' . $endpoint . '_' . $type;
                    $routes->add($name, $route);
                }
            }
        }

        $this->loaded = true;

        return $routes;
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
