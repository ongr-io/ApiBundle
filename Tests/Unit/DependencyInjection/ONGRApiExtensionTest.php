<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Unit\DependencyInjection;

use ONGR\ApiBundle\DependencyInjection\ONGRApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Tests for ONGRApiExtension.
 */
class ONGRApiExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ONGRApiExtension
     */
    private static $extension;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$extension = new ONGRApiExtension();
    }

    /**
     * Provider for testConfiguration.
     *
     * @return array
     */
    public function configurationProvider()
    {
        return [
            // Case #0. Invalid manager.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => null,
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                'Manager must be set, when using default controller. (Endpoint: \'persons\')',
            ],
            // Case #1. Invalid document.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => null,
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                'Document must be set, when using default controller. (Endpoint: \'persons\')',
            ],
            // Case #2. Invalid include/exclude configuration.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'include_fields' => ['foo'],
                                    'exclude_fields' => ['bar'],
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                '\'include_fields\' and \'exclude_fields\' can not be used together in endpoint \'persons\'.',
            ],
            // Case #3. Version inheritance.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                                'people' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'include_fields' => ['name', 'age'],
                                    'controller' => ['name' => 'notDefault'],
                                ],
                            ],
                        ],
                        'v2' => [
                            'endpoints' => [
                                'people_names' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'include_fields' => ['name'],
                                ],
                            ],
                            'parent' => 'v1',
                        ],

                        'v3' => [
                            'parent' => 'v2',
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.persons' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.people' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v2.people_names' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name'],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v2.persons' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v2.people' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v3.people_names' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name'],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v3.persons' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v3.people' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                ],
            ],

            // Case #4. Version inherited endpoint overriding.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                                'people' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'include_fields' => ['name', 'age'],
                                    'controller' => ['name' => 'notDefault'],
                                ],
                            ],
                        ],
                        'v2' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.not_default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'include_fields' => ['name', 'age', 'profession'],
                                ],
                            ],
                            'parent' => 'v1',
                        ],

                        'v3' => [
                            'endpoints' => [
                                'people' => [
                                    'manager' => 'es.manager.not_default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'include_fields' => ['name', 'age'],
                                    'controller' => ['name' => 'custom'],
                                ],
                            ],
                            'parent' => 'v2',
                        ],

                        'v4' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.custom',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                    'exclude_fields' => ['id'],
                                ],
                            ],
                            'parent' => 'v3',
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.persons' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.people' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v2.persons' => [
                        'manager' => 'es.manager.not_default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age', 'profession'],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v2.people' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v3.persons' => [
                        'manager' => 'es.manager.not_default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age', 'profession'],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v3.people' => [
                        'manager' => 'es.manager.not_default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'custom',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v4.persons' => [
                        'manager' => 'es.manager.custom',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => [],
                        'exclude_fields' => ['id'],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v4.people' => [
                        'manager' => 'es.manager.not_default',
                        'document' => 'AcmeTestBundle:PersonDocument',
                        'include_fields' => ['name', 'age'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'custom',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                ],
            ],
            // Case #5. Cyclical version inheritance.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                            'parent' => 'v4',
                        ],
                        'v2' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                            'parent' => 'v1',
                        ],
                        'v3' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                            'parent' => 'v2',
                        ],
                        'v4' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                            'parent' => 'v3',
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                '\'v4\' can not be ancestor of itself.',
            ],
            // Case #6. Empty version.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [],
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                'At least one endpoint must be configured in version \'v1\'.',
            ],
            // Case #7. Version parent pointing to itself.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                            'parent' => 'v1',
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                '\'v1\' can not be ancestor of itself.',
            ],
            // Case #8. Invalid version parent.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:PersonDocument',
                                ],
                            ],
                            'parent' => 'not_exist',
                        ],
                    ],
                ],
                // Expected.
                [],
                // Exception.
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
                // Exception message.
                'Invalid parent \'not_exist\'.',
            ],
        ];
    }

    /**
     * Tests whether parent configuration is inherited correctly.
     *
     * @param array  $config
     * @param array  $expectedConfig
     * @param string $expectedException
     * @param string $expectedExceptionMessage
     *
     * @dataProvider configurationProvider
     */
    public function testConfiguration(
        $config,
        $expectedConfig,
        $expectedException = null,
        $expectedExceptionMessage = null
    ) {
        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $container = $this->getDIContainer();
        self::$extension->load([$config], $container);

        foreach ($expectedConfig as $id => $value) {
            $this->assertEquals($container->getParameter($id), $value, "{$id} does not match expected value.");
        }
    }

    /**
     * @return ContainerBuilder
     */
    private function getDIContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }
}
