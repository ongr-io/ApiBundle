<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PostGetEvent.
 */
class PostGetEvent extends Event
{
    /**
     * @var array
     */
    private $result;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     * @param array   $result
     */
    public function __construct(Request $request, $result)
    {
        $this->request = $request;
        $this->setResult($result);
    }

    /**
     * @return array
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param array $result
     *
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}
