<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\Service;

use ONGR\ApiBundle\Service\BatchRequestHandler;
use ONGR\ApiBundle\Service\Crud;
use Symfony\Component\HttpFoundation\Request;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

class BatchRequestHandlerTest extends AbstractElasticsearchTestCase
{
    private $data = [
        [
            'manufacturer' => 'diesel',
        ],
        [
            'manufacturer' => 'levis',
        ],
        [
            'manufacturer' => 'armani',
        ],
    ];

    /**
     * Test batch post API with more data than the bulkCommitSize
     */
    public function testHandleRequestWithMoreDataThanCommitSize()
    {
        $manager = $this->getManager();
        $data = json_encode($this->data);
        $crud = new Crud();
        $repository = $manager->getRepository('ONGR\ApiBundle\Tests\app\fixture\TestBundle\Document\Jeans');
        $serializer = $this->getContainer()->get('ongr_api.request_serializer');

        $manager->setBulkCommitSize(2);

        $request = Request::create(
            '/api/v3/jeans/_batch',
            'POST',
            [],
            [],
            [],
            [],
            $data
        );
        $request->headers->set('Accept', ['application/json', 'text/json']);

        $handler = new BatchRequestHandler($crud, $manager, $serializer);
        $response = $handler->handleRequest($request, $repository, 'create');
        $response = json_decode($response, true);

        $this->assertEquals(3, count($response));
        foreach ($response as $item) {
            $this->assertTrue(isset($item['items'][0]['create']['_id']));
        }
    }
}
