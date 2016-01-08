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

class VariantsControllerTest extends BasicControllerTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'tshirt' => [
                    [
                        '_id' => 1,
                        'manufacturer' => 'NIKE',
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
                        'manufacturer' => 'Fox',
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
                        'manufacturer' => 'Cloth',
                        'variants' => [],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function getTestGetAllVariantsDate()
    {
        $cases = [];

        $cases['Document with id 1'] = [
            '/api/v3/tshirt/1/_variant',
            json_encode(
                [
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
                ]
            )
        ];

        $cases['Document with id 2'] = [
            '/api/v3/tshirt/2/_variant',
            json_encode(
                [
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
                ]
            )
        ];

        return $cases;
    }

    /**
     * @dataProvider getTestGetAllVariantsDate()
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
            '/api/v3/tshirt/3/_variant',
            $variants
        );

        $this->assertEquals(
            $variants,
            $this->sendApiRequest(Request::METHOD_GET, '/api/v3/tshirt/3/_variant')->getContent()
        );
    }

    /**
     * Test for delete request.
     */
    public function testSendDeleteVariants()
    {
        $this->sendApiRequest(
            Request::METHOD_DELETE,
            '/api/v3/tshirt/2/_variant/0'
        );

        $this->assertEquals(
            json_encode(
                [
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
                    ]
                ]
            ),
            $this->sendApiRequest(Request::METHOD_GET, '/api/v3/tshirt/2/_variant')->getContent()
        );
    }
}
