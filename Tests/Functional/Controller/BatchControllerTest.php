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

class BatchControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'person' => [
                    [
                        '_id' => 1,
                        'name' => 'TestName1',
                        'surname' => 'TestSurname1',
                    ],
                    [
                        '_id' => 2,
                        'name' => 'TestName2',
                        'surname' => 'TestSurname2',
                    ],
                    [
                        '_id' => 3,
                        'name' => 'TestName3',
                        'surname' => 'TestSurname3',
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests if batch works as expected.
     */
    public function testBatchAction()
    {
        $response = $this->sendBatchRequest(json_encode($this->getBatchContent()));

        $this->assertEquals(Response::HTTP_ACCEPTED, $response->getStatusCode());
        $this->assertEquals($this->getBatchResponse(), json_decode($response->getContent(), true));
    }

    /**
     * Batch request content.
     *
     * @return array
     */
    private function getBatchContent()
    {
        return [
            [
                'method' => 'POST',
                'path' => 'person/4',
                'body' => [
                    'name' => 'foo_name',
                    'surname' => 'tuna_surname',
                ],
            ],
            [
                'method' => 'GET',
                'path' => 'person/1',
                'body' => [],
            ],
            [
                'method' => 'GET',
                'path' => 'person/5',
                'body' => [],
            ],
            [
                'method' => 'DELETE',
                'path' => 'person/1',
                'body' => [],
            ],
            [
                'method' => 'GET',
                'path' => 'person/1',
                'body' => [],
            ],
            [
                'method' => 'PUT',
                'path' => 'person/2',
                'body' => [
                    'name' => 'updated_name',
                    'surname' => 'updated_surname',
                ],
            ],
            [
                'method' => 'GET',
                'path' => 'person/2',
                'body' => [],
            ],
        ];
    }

    /**
     * Batch request response.
     *
     * @return array
     */
    private function getBatchResponse()
    {
        return [
            ['status_code' => Response::HTTP_CREATED],
            [
                'status_code' => Response::HTTP_OK,
                'response' => [
                    'name' => 'TestName1',
                    'surname' => 'TestSurname1',
                ],
            ],
            ['status_code' => Response::HTTP_GONE],
            ['status_code' => Response::HTTP_NO_CONTENT],
            ['status_code' => Response::HTTP_GONE],
            ['status_code' => Response::HTTP_NO_CONTENT],
            [
                'status_code' => Response::HTTP_OK,
                'response' => [
                    'id' => 2,
                    'name' => 'updated_name',
                    'surname' => 'updated_surname',
                ],
            ],
        ];
    }

    /**
     * Tests batch request providing invalid path.
     */
    public function testBatchWithInvalidPath()
    {
        $content = [
            [
                'method' => 'GET',
                'path' => 'unknown/1',
                'body' => [],
            ],
        ];

        $respose = $this->sendBatchRequest(json_encode($content));
        $this->assertEquals(Response::HTTP_ACCEPTED, $respose->getStatusCode());
        $this->assertEquals(
            [
                [
                    'status_code' => Response::HTTP_BAD_REQUEST,
                    'message' => 'Could not resolve path!',
                    'error' => '',
                ],
            ],
            json_decode($respose->getContent(), true)
        );
    }

    /**
     * Tests batch action providing invalid json.
     */
    public function testBatchWithInvalidContent()
    {
        $respose = $this->sendBatchRequest('thisisinvalidjson_O;.;o');
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $respose->getStatusCode());
        $this->assertEquals(
            ['message' => 'Deserialization error!'],
            json_decode($respose->getContent(), true)
        );
    }

    /**
     * Sends batch api request.
     *
     * @param string $content
     *
     * @return null|Response
     */
    private function sendBatchRequest($content)
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/v2/batch',
            [],
            [],
            [
                'HTTP_Authorization' => 'superdupersecretkey#pleasedonttellitanyone',
                'HTTP_Accept' => 'application/json',
            ],
            $content
        );

        return $client->getResponse();
    }
}
