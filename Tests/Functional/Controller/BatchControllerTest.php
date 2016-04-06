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

use ONGR\ElasticsearchBundle\Result\Result;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use Symfony\Component\HttpFoundation\Request;

class BatchControllerTest extends AbstractControllerTestCase
{
    /**
     * Provides data for testBatchRequest method
     *
     * @return array
     */
    public function getBatchRequestData()
    {
        $postData = [
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

        $putData = [
            [
                '_id' => 1,
                'manufacturer' => 'diesel',
            ],
            [
                '_id' => 2,
                'manufacturer' => 'levis',
            ],
            [
                '_id' => 3,
                'manufacturer' => 'armani',
            ],
        ];

        $out = [];
        $out[] = [$postData, Request::METHOD_POST];
        $out[] = [$putData, Request::METHOD_PUT];
        $out[] = [$putData, Request::METHOD_DELETE];
        return $out;
    }


    /**
     * Test batch API by sending data batch.
     * @param array  $data
     * @param string $method
     *
     * @dataProvider getBatchRequestData
     */
    public function testBatchRequest($data, $method)
    {
        $this->getManager();
        $data_json = json_encode($data);

        if ($method == 'DELETE') {
            $this->sendApiRequest(
                'PUT',
                '/api/v3/jeans/_batch',
                $data_json
            );
        }

        $this->sendApiRequest(
            $method,
            '/api/v3/jeans/_batch',
            $data_json
        );

        $repo = $this->getManager()->getRepository('TestBundle:Jeans');
        $search = $repo->createSearch()->addQuery(new MatchAllQuery());
        $results = $repo->execute($search, Result::RESULTS_ARRAY);
        if ($method == 'PUT') {
            foreach ($data as $key => $item) {
                unset($item['_id']);
                $data[$key] = $item;
            }
        }
        sort($data);
        sort($results);
        if ($method == 'DELETE') {
            $this->assertEmpty($results);
        } else {
            $this->assertEquals($data, $results);
        }
    }
}
