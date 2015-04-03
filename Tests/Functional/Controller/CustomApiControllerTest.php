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

use ONGR\ApiBundle\Tests\Functional\AbstractTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests for CustomApiController.
 */
class CustomApiControllerTest extends AbstractTestCase
{
    /**
     * @var Client.
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * Data provider for testMethods.
     *
     * @return array
     */
    public function dataProvider()
    {
        return [
            // General test for get.
            [
                'POST',
                '/v3/persons',
                'Custom controller POST',
                Response::HTTP_OK,
            ],
            [
                'GET',
                '/v3/persons',
                'Custom controller GET',
                Response::HTTP_OK,
            ],
            [
                'PUT',
                '/v3/persons',
                'Custom controller PUT',
                Response::HTTP_OK,
            ],
            [
                'DELETE',
                '/v3/persons',
                'Custom controller DELETE',
                Response::HTTP_OK,
            ],
        ];
    }

    /**
     * Test GET.
     *
     * @param string $method
     * @param string $path
     * @param array  $expectedResponse
     * @param int    $statusCode
     *
     * @dataProvider dataProvider
     */
    public function testMethods($method, $path, $expectedResponse, $statusCode = Response::HTTP_OK)
    {
        $this->client->request($method, $path);
        $this->assertEquals(
            $statusCode,
            $this->client->getResponse()->getStatusCode()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($response, $expectedResponse);
    }
}
