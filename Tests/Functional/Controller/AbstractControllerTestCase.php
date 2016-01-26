<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\Controller;

use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\HttpFoundation\Response;

class AbstractControllerTestCase extends AbstractElasticsearchTestCase
{
    /**
     * Sends api request.
     *
     * @param string $method
     * @param string $uri
     * @param string $content
     *
     * @return null|Response
     */
    protected function sendApiRequest($method, $uri, $content = null)
    {
        $client = static::createClient();
        $client->request(
            $method,
            $uri,
            [],
            [],
            [
                'HTTP_Accept' => 'application/json',
            ],
            $content
        );

        return $client->getResponse();
    }
}