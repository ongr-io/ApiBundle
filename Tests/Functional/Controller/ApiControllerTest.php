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
 * Tests for ApiController.
 */
class ApiControllerTest extends AbstractTestCase
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
     * Data provider for GET test.
     *
     * @return array
     */
    public function providerForGet()
    {
        return [
            // Case #0. General test for get.
            [
                '/v1/persons',
                [
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
            // Case #1. Test included fields.
            [
                '/v1/people_names',
                [
                    [
                        'name' => 'TestName1',
                    ],
                    [
                        'name' => 'TestName2',
                    ],
                    [
                        'name' => 'TestName3',
                    ],
                ],
            ],
            // Case #2. Test excluded fields.
            [
                '/v1/people_surnames',
                [
                    [
                        'surname' => 'TestSurname1',
                    ],
                    [
                        'surname' => 'TestSurname2',
                    ],
                    [
                        'surname' => 'TestSurname3',
                    ],
                ],
            ],
            // Case #3. Test parent handling for endpoint.
            [
                '/v2/people',
                [
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
            // Case #4. Test Custom controller.
            [
                '/v3/persons',
                'Custom controller GET',
            ],
        ];
    }

    /**
     * Test GET.
     *
     * @param string $path
     * @param array  $expectedResponse
     *
     * @dataProvider providerForGet
     */
    public function testGet($path, $expectedResponse)
    {
        $this->client->request('GET', $path);
        $this->assertEquals(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals($response, $expectedResponse);
    }

    /**
     * Data provider for statusCode test.
     *
     * @return array
     */
    public function providerForStatusCode()
    {
        return [
            // Case #0.
            ['PUT', '/v1/persons', Response::HTTP_NOT_FOUND, false],
            // Case #1.
            ['PUT', '/v2/people', Response::HTTP_NOT_FOUND, false],
            // Case #2.
            ['PUT', '/v3/persons', Response::HTTP_OK],
            // Case #3.
            ['GET', '/v1/persons', Response::HTTP_OK],
            // Case #4.
            ['GET', '/v2/people', Response::HTTP_OK],
            // Case #5.
            ['GET', '/v3/persons', Response::HTTP_OK],
            // Case #6.
            ['POST', '/v1/persons', Response::HTTP_NOT_FOUND, false],
            // Case #7.
            ['POST', '/v2/people', Response::HTTP_NOT_FOUND, false],
            // Case #8.
            ['POST', '/v3/persons', Response::HTTP_OK],
            // Case #9.
            ['DELETE', '/v1/persons', Response::HTTP_NOT_FOUND, false],
            // Case #10.
            ['DELETE', '/v2/people', Response::HTTP_NOT_FOUND, false],
            // Case #11.
            ['DELETE', '/v3/persons', Response::HTTP_OK],
        ];
    }

    /**
     * Test status code.
     *
     * @param string $method
     * @param string $path
     * @param int    $statusCode
     * @param bool   $equals
     *
     * @dataProvider providerForStatusCode
     */
    public function testStatusCode($method, $path, $statusCode, $equals = true)
    {
        $this->client->request($method, $path);

        if ($equals) {
            $this->assertEquals(
                $statusCode,
                $this->client->getResponse()->getStatusCode()
            );
        } else {
            $this->assertNotEquals(
                $statusCode,
                $this->client->getResponse()->getStatusCode()
            );
        }
    }
}
