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

use ONGR\ApiBundle\Tests\app\fixture\TestBundle\Document\Person;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\HttpFoundation\Response;

class RestControllerTest extends AbstractElasticsearchTestCase
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
                        'active' => false,
                    ],
                    [
                        '_id' => 2,
                        'name' => 'TestName2',
                        'surname' => 'TestSurname2',
                        'active' => true,
                    ],
                    [
                        '_id' => 3,
                        'name' => 'TestName3',
                        'surname' => 'TestSurname3',
                        'active' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests get api when document is found.
     */
    public function testGetApiWithId()
    {
        $this->getManager();
        $response = $this->sendApiRequest('GET', '/api/v3/person/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            '{"name":"TestName1","surname":"TestSurname1","active":false}',
            $response->getContent()
        );
    }

    /**
     * Tests get api when document without id.
     *
     */
    public function testGetApiWithoutId()
    {
        $this->getManager();
        $response = $this->sendApiRequest('GET', '/api/v3/person');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertArrayHasKey('errors', json_decode($response->getContent(), true));
    }

    /**
     * Tests get api when document is not found.
     */
    public function testGetApiNotFound()
    {
        $this->getManager();
        $response = $this->sendApiRequest('GET', '/api/v3/person/4');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertArrayHasKey('errors', json_decode($response->getContent(), true));
    }

    /**
     * Tests post api without providing any identifier.
     */
    public function testPostApiWithoutId()
    {
        $this->getManager();
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/person',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('{"name":"foo_name"}', $response->getContent());
    }

    /**
     * Test post api with document id passed, inc headers.
     */
    public function testPostApiWithId()
    {
        $manager = $this->getManager();

        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                        'surname' => 'foo_surname',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($manager->find('TestBundle:Person', 4));
    }

    /**
     * Tests post api including unknown field.
     */
//    public function testPostApiWithUnknownField()
//    {
//        $manager = $this->getManager();
//        $response = $this
//            ->sendApiRequest(
//                'POST',
//                '/api/v3/person/4',
//                json_encode(
//                    [
//                        'name' => 'foo_name',
//                        'unknown' => 'Don\'t know this',
//                    ]
//                )
//            );
//
//        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
//        $this->assertEquals(
//            '{"message":"Validation error!","errors":["This form should not contain extra fields."]}',
//            $response->getContent()
//        );
//        $this->assertNull($manager->getRepository('TestBundle:Person')->find(4));
//    }


    /**
     * Tests post api twice providing identifier in request body.
     */
    public function testPostApiTwice()
    {
        $manager = $this->getManager();
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'Resource should be created!');
        $this->assertNotNull($manager->find('TestBundle:Person', 4), 'Document should exist!');

        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode(), 'Resource exists!');
    }

    /**
     * Tests put api without providing any identifier.
     */
    public function testPutApiWithoutId()
    {
        $this->getManager();
        $response = $this
            ->sendApiRequest(
                'PUT',
                '/api/v3/person',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertArrayHasKey('errors', json_decode($response->getContent(), true));
    }

    /**
     * Tests put api with document id passed.
     */
    public function testPutApiWithId()
    {
        $manager = $this->getManager();
        $response = $this
            ->sendApiRequest(
                'PUT',
                '/api/v3/person/2',
                json_encode(
                    [
                        'name' => 'foo_name',
                        'active' => false
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        /** @var Person $document */
        $document = $manager->find('TestBundle:Person', 2);

        $this->assertNotNull($document, 'Document should exist');
        $this->assertEquals('foo_name', $document->getName(), 'Document \'name\' property should have changed');
        $this->assertFalse($document->isActive(), 'Document \'active\' property should have changed');
    }

    /**
     * Tests put api including unknown field.
     */
//    public function testPutApiWithUnknownField()
//    {
//        $this->getManager();
//        $response = $this
//            ->sendApiRequest(
//                'PUT',
//                '/api/v1/custom/person/2',
//                json_encode(
//                    [
//                        'name' => 'foo_name',
//                        'unknown' => 'Don\'t know this',
//                    ]
//                )
//            );
//
//        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
//        $this->assertEquals(
//            '{"message":"Validation error!","errors":["This form should not contain extra fields."]}',
//            $response->getContent()
//        );
//    }


    /**
     * Tests delete api when document is found.
     */
    public function testDeleteApiSuccess()
    {
        $manager = $this->getManager();
        $response = $this->sendApiRequest('DELETE', '/api/v3/person/1');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertNull($manager->find('TestBundle:Person', 1));
    }

    /**
     * Tests delete api when document is not found.
     */
    public function testDeleteApiNotFound()
    {
        $this->getManager();
        $response = $this->sendApiRequest('DELETE', '/api/v3/person/4');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Tests delete api when no document id is given.
     */
    public function testDeleteApiWithoutId()
    {
        $this->getManager();
        $response = $this->sendApiRequest('DELETE', '/api/v3/person');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Sends api request.
     *
     * @param string $method
     * @param string $uri
     * @param string $content
     *
     * @return null|Response
     */
    private function sendApiRequest($method, $uri, $content = null)
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
