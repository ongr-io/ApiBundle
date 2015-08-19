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
use Symfony\Component\Routing\RouteCollection;

/**
 * Loads api routes.
 */
class ElasticsearchLoader extends Loader
{
    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @var RouteCollection
     */
    private $collection;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "ongr_api" loader twice');
        }

        return $this->getCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return 'ongr_api' === $type;
    }

    /**
     * @return RouteCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param RouteCollection $collection
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }
}
