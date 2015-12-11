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
use ONGR\ElasticsearchBundle\Document\AbstractDocument;

/**
 * Class Person Document.
 *
 * @ES\Document(type="person")
 */
class Person extends AbstractDocument
{
    /**
     * @var string
     *
     * @ES\Property(name="name", type="string", options={"index"="not_analyzed"})
     */
    public $name;

    /**
     * @var string
     *
     * @ES\Property(name="surname", type="string", options={"index"="not_analyzed"})
     */
    public $surname;

    /**
     * @var string
     *
     * @ES\Property(name="age", type="integer")
     */
    public $age;

    /**
     * @var boolean
     *
     * @ES\Property(name="active", type="boolean")
     */
    public $active;
}
