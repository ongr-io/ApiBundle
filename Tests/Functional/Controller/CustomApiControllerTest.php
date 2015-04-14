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
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tests for CustomApiController.
 */
class CustomApiControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * @var Client.
     */
    private $client;

    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'person' => [
                    [
                        'name' => 'TestName1',
                        'surname' => 'TestSurname1',
                    ],
                    [
                        'name' => 'TestName2',
                        'surname' => 'TestSurname2',
                    ],
                    [
                        'name' => 'TestName3',
                        'surname' => 'TestSurname3',
                    ],
                ],
            ],
        ];
    }

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
            // Case #0.
            [
                'POST',
                '/v3/persons',
                'Custom controller POST',
            ],
            // Case #1.
            [
                'GET',
                '/v3/persons',
                'Custom controller GET',
            ],
            // Case #2.
            [
                'PUT',
                '/v3/persons',
                'Custom controller PUT',
            ],
            // Case #3.
            [
                'DELETE',
                '/v3/persons',
                'Custom controller DELETE',
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
