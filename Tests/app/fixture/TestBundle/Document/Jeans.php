<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\app\fixture\TestBundle\Document;

use ONGR\ElasticsearchBundle\Annotation as ES;
use ONGR\ElasticsearchBundle\Collection\Collection;

/**
 * @ES\Document(type="jeans")
 */
class Jeans
{
    /**
     * @ES\Id()
     */
    public $id;

    /**
     * @var
     *
     * @ES\Property(type="string")
     */
    public $manufacturer;

    /**
     * @var Collection
     *
     * @ES\Embedded(class="TestBundle:JeansVariant", multiple=true)
     */
    public $variants;

    public function __construct()
    {
        $this->variants = new Collection();
    }
}