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
     * Test batch API by sending data batch.
     */
    public function testBatchRequest()
    {
        $this->getManager();
        $data = json_encode($this->data);

        $response = $this->sendApiRequest(
            Request::METHOD_POST,
            '/api/v3/jeans/_batch',
            $data
        );

        $repo = $this->getManager()->getRepository('TestBundle:Jeans');
        $search = $repo->createSearch()->addQuery(new MatchAllQuery());
        $results = $repo->execute($search, Result::RESULTS_ARRAY);

//        $resultsFromApi = [];
//        foreach ($results as $result) {
//            $resultsFromApi[] = $result['manufacturer'];
//        }
//
//        $originalData = ['diesel']

        $this->assertEquals(asort($this->data), asort($results));
    }
}
