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

class RestControllerTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'not_default' => [
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
     * Tests if authorization is currently working.
     */
    public function testAuthorization()
    {
        $client = static::createClient();
        $client->request('GET', '/api/v1/person');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * Tests delete api when document is found.
     */
    public function testDeleteApiSuccess()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('DELETE', '/api/v1/custom/person/1');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertNull($manager->getRepository('AcmeTestBundle:Person')->find(1));
    }

    /**
     * Tests delete api when document is not found.
     */
    public function testDeleteApiNotFound()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('DELETE', '/api/v1/custom/person/4');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Tests delete api when no document id is given.
     */
    public function testDeleteApiWithoutId()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('DELETE', '/api/v1/custom/person');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * Tests get api when document is found.
     */
    public function testGetApiWithId()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('GET', '/api/v1/custom/person/1');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            '{"name":"TestName1","surname":"TestSurname1"}',
            $response->getContent()
        );
    }

    /**
     * Tests get api when document without id.
     */
    public function testGetApiWithoutId()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('GET', '/api/v1/custom/person');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            '[{"name":"TestName1","surname":"TestSurname1"},'
            . '{"name":"TestName2","surname":"TestSurname2"},'
            . '{"name":"TestName3","surname":"TestSurname3"}]',
            $response->getContent()
        );
    }

    /**
     * Tests get api when document without id, but including range in query string.
     */
    public function testGetApiWithQueryStringRange()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('GET', '/api/v1/custom/person?from=1&size=2');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            '[{"name":"TestName2","surname":"TestSurname2"},'
            . '{"name":"TestName3","surname":"TestSurname3"}]',
            $response->getContent()
        );
    }

    /**
     * Tests get api when document is not found.
     */
    public function testGetApiNotFound()
    {
        $manager = $this->getManager('not_default');
        $response = $this->sendApiRequest('GET', '/api/v1/custom/person/4');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * Test post api with document id passed, inc headers.
     */
    public function testPostApiWithId()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v1/custom/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                        'surname' => 'foo_surname',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertNotNull($manager->getRepository('AcmeTestBundle:Person')->find(4));
        $this->assertTrue($response->headers->has('Location'));
        $this->assertEquals('/api/v1/custom/person/4', $response->headers->get('Location'));
    }

    /**
     * Tests post api including unknown field.
     */
    public function testPostApiWithUnknownField()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v1/custom/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                        'unknown' => 'Dont know this',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
        $this->assertEquals(
            '{"message":"Validation error!","errors":["This form should not contain extra fields."]}',
            $response->getContent()
        );
        $this->assertNull($manager->getRepository('AcmeTestBundle:Person')->find(4));
    }

    /**
     * Tests post api without providing any identifier.
     */
    public function testPostApiWithoutId()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v1/custom/person',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('{"message":"Identifier not found!"}', $response->getContent());
    }

    /**
     * Tests post api twice providing identifier in request body.
     */
    public function testPostApiTwice()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v1/custom/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'Resource should be created!');
        $this->assertNotNull($manager->getRepository('AcmeTestBundle:Person')->find(4), 'Document should exist!');

        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v1/custom/person/4',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode(), 'Resource should exist!');
    }

    /**
     * Tests put api with document id passed.
     */
    public function testPutApiWithId()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'PUT',
                '/api/v1/custom/person/2',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode()
        );
        $document = $manager->getRepository('AcmeTestBundle:Person')->find(2);
        $this->assertNotNull($document, 'Document should exist');
        $this->assertEquals('foo_name', $document->getName(), 'Document property should have changed');
        $this->assertTrue($response->headers->has('Location'), 'Response should contain Location header');
        $this->assertEquals(
            '/api/v1/custom/person/2',
            $response->headers->get('Location'),
            'Response Location header is invalid'
        );
    }

    /**
     * Tests put api including unknown field.
     */
    public function testPutApiWithUnknownField()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'PUT',
                '/api/v1/custom/person/2',
                json_encode(
                    [
                        'name' => 'foo_name',
                        'unknown' => 'Dont know this',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
        $this->assertEquals(
            '{"message":"Validation error!","errors":["This form should not contain extra fields."]}',
            $response->getContent()
        );
    }

    /**
     * Tests put api without providing any identifier.
     */
    public function testPutApiWithoutId()
    {
        $manager = $this->getManager('not_default');
        $response = $this
            ->sendApiRequest(
                'PUT',
                '/api/v1/custom/person',
                json_encode(
                    [
                        'name' => 'foo_name',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertEquals('{"message":"Identifier not found!"}', $response->getContent());
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
                'HTTP_Authorization' => 'superdupersecretkey#pleasedonttellitanyone',
                'HTTP_Accept' => 'application/json',
            ],
            $content
        );

        return $client->getResponse();
    }
}
