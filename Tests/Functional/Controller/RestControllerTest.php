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

use ONGR\ApiBundle\Tests\app\fixture\TestBundle\Document\Jeans;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\HttpFoundation\Response;

class RestControllerTest extends AbstractControllerTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'jeans' => [
                    [
                        '_id' => 1,
                        'manufacturer' => 'armani',
                    ],
                    [
                        '_id' => 2,
                        'manufacturer' => 'levis',
                    ],
                    [
                        '_id' => 3,
                        'manufacturer' => 'denim',
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests get api when document is found.
     */
    public function testGetApi()
    {
        $response = $this->sendApiRequest('GET', '/api/v3/jeans/1');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            '{"manufacturer":"armani"}',
            $response->getContent()
        );
    }

    /**
     * Tests get api when document is not found.
     */
    public function testGetApiNotFound()
    {
        $response = $this->sendApiRequest('GET', '/api/v3/jeans/4');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertArrayHasKey('errors', json_decode($response->getContent(), true));
    }

    /**
     * Tests post api without providing any identifier.
     */
    public function testPostApiWithoutId()
    {
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/jeans',
                json_encode(
                    [
                        'manufacturer' => 'armani',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals('{"manufacturer":"armani"}', $response->getContent());
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
                '/api/v3/jeans/41',
                json_encode(
                    [
                        'manufacturer' => 'armani',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $document = $manager->find('TestBundle:Jeans', 41);
        $this->assertNotNull($document);
        $this->assertEquals('armani', $document->manufacturer);
    }

    /**
     * Tests post api twice providing identifier in request body.
     */
    public function testPostApiTwice()
    {
        $manager = $this->getManager();
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/jeans/4',
                json_encode(
                    [
                        'manufacturer' => 'armani',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), 'Resource should be created!');
        $this->assertNotNull($manager->find('TestBundle:Jeans', 4), 'Document should exist!');

        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/jeans/4',
                json_encode(
                    [
                        'manufacturer' => 'armani',
                    ]
                )
            );
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode(), 'Resource exists!');
    }

    /**
     * Tests put api.
     */
    public function testPutApi()
    {
        $manager = $this->getManager();
        /** @var Jeans $document */
        $document = $manager->find('TestBundle:Jeans', 1);
        $this->assertNotNull($document, 'Document should exist');
        $this->assertEquals('armani', $document->manufacturer);

        $response = $this
            ->sendApiRequest(
                'PUT',
                '/api/v3/jeans/1',
                json_encode(
                    [
                        'manufacturer' => 'levis',
                    ]
                )
            );

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        /** @var Jeans $document */
        $document = $manager->find('TestBundle:Jeans', 1);
        $this->assertNotNull($document, 'Document should exist');
        $this->assertEquals('levis', $document->manufacturer);
    }

    /**
     * Tests delete api when document is found.
     */
    public function testDeleteApiSuccess()
    {
        $manager = $this->getManager();
        $response = $this->sendApiRequest('DELETE', '/api/v3/jeans/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->assertNull($manager->find('TestBundle:Jeans', 1));
    }

    /**
     * Tests delete api when document is not found.
     */
    public function testDeleteApiNotFound()
    {
        $response = $this->sendApiRequest('DELETE', '/api/v3/jeans/4');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertTrue(strpos($response->getContent(), 'Identifier not found!') !== false);
    }

    /**
     * Tests post with non existing property
     */
    public function testPostApiWithNotExistingProperty()
    {
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/jeans/5',
                json_encode(
                    [
                        'manufacturer' => 'armani',
                        'non_existing_property' => ''
                    ]
                )
            );
        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
        $this->assertTrue(
            strpos($response->getContent(), 'Property `non_existing_property` does not exist') !== false
        );
    }

    /**
     * Tests post with non existing property
     */
    public function testPostApiWithNotAllowedField()
    {
        $response = $this
            ->sendApiRequest(
                'POST',
                '/api/v3/jeans/5',
                json_encode(
                    [
                        'manufacturer' => 'armani',
                        'designer' => ''
                    ]
                )
            );
        $this->assertEquals(Response::HTTP_NOT_ACCEPTABLE, $response->getStatusCode());
        $this->assertTrue(
            strpos($response->getContent(), 'You are not allowed to insert or modify the field') !== false
        );
    }

    /**
     * Tests get all api
     */
    public function testAllRequest()
    {
        $response = $this->sendApiRequest('GET', '/api/v3/jeans/_all');
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals(
            '[{"manufacturer":"armani"},{"manufacturer":"levis"},{"manufacturer":"denim"}]',
            $response->getContent()
        );

        $response = $this->sendApiRequest('GET', '/api/v3/jeans/_all?size=1');
        $this->assertEquals('[{"manufacturer":"armani"}]', $response->getContent());

        $response = $this->sendApiRequest('GET', '/api/v3/jeans/_all?from=2');
        $this->assertEquals('[{"manufacturer":"denim"}]', $response->getContent());
    }
}
