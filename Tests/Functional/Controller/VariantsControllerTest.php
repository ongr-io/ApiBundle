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

use Symfony\Component\HttpFoundation\Request;

class VariantsControllerTest extends AbstractControllerTestCase
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
                        'variants' => [
                            [
                                'color' => 'black',
                                'size' => 'S',
                            ],
                            [
                                'color' => 'black',
                                'size' => 'M',
                            ],
                            [
                                'color' => 'black',
                                'size' => 'L',
                            ],
                            [
                                'color' => 'blue',
                                'size' => 'L',
                            ],
                        ],
                    ],
                    [
                        '_id' => 2,
                        'manufacturer' => 'levis',
                        'variants' => [
                            [
                                'color' => 'red',
                                'size' => 'S',
                            ],
                            [
                                'color' => 'red',
                                'size' => 'M',
                            ],
                            [
                                'color' => 'white',
                                'size' => 'L',
                            ],
                            [
                                'color' => 'blue',
                                'size' => 'XL',
                            ],
                        ],
                    ],
                    [
                        '_id' => 3,
                        'manufacturer' => 'diesel',
                        'variants' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getTestGetAllVariantsData()
    {
        $cases = [];

        $cases['Document with id 1'] = [
            '/api/v3/jeans/1/_variant',
            json_encode($this->getDataArray()['default']['jeans'][0]['variants'])
        ];

        $cases['Document with id 2'] = [
            '/api/v3/jeans/2/_variant',
            json_encode($this->getDataArray()['default']['jeans'][1]['variants'])
        ];

        return $cases;
    }

    /**
     * @dataProvider getTestGetAllVariantsData()
     */
    public function testGetAllVariants($uri, $expectedVariants)
    {
        $this->assertEquals(
            $expectedVariants,
            $this->sendApiRequest(Request::METHOD_GET, $uri)->getContent()
        );
    }

    /**
     * Test for post request.
     */
    public function testSendPostVariants()
    {
        $variants = json_encode(
            [
                [
                    'color' => 'black',
                    'size' => 'L',
                ],
                [
                    'color' => 'black',
                    'size' => 'XL',
                ],
            ]
        );

        $this->sendApiRequest(
            Request::METHOD_POST,
            '/api/v3/jeans/3/_variant',
            $variants
        );

        $this->assertEquals(
            $variants,
            $this->sendApiRequest(Request::METHOD_GET, '/api/v3/jeans/3/_variant')->getContent()
        );
    }

    /**
     * Test for delete request.
     */
    public function testSendDeleteVariants()
    {
        $variants = $this->getDataArray()['default']['jeans'][1]['variants'];

        $this->assertEquals(
            json_encode($variants),
            $this->sendApiRequest(Request::METHOD_GET, '/api/v3/jeans/2/_variant')->getContent()
        );

        $this->sendApiRequest(
            Request::METHOD_DELETE,
            '/api/v3/jeans/2/_variant/0'
        );

        unset($variants[0]);
        $variants = array_values($variants);

        $this->assertEquals(
            json_encode($variants),
            $this->sendApiRequest(Request::METHOD_GET, '/api/v3/jeans/2/_variant')->getContent()
        );
    }

    /**
     * Test for put request.
     */
    public function testSendPutVariants()
    {
        $variant = json_encode(['color' => 'transparent', 'size' => 'M']);

        $this->sendApiRequest(
            Request::METHOD_PUT,
            '/api/v3/jeans/2/_variant/0',
            $variant
        );

        $this->assertEquals(
            $variant,
            $this->sendApiRequest(Request::METHOD_GET, '/api/v3/jeans/2/_variant/0')->getContent()
        );
    }
}
