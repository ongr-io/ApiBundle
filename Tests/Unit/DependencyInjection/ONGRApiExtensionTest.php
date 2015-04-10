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
            // Case #0. All fields.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                ],
                                'child' => [
                                    'parent' => 'parent',
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                ],
            ],
            // Case #1. Override some fields.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                ],
                                'child' => [
                                    'parent' => 'parent',
                                    'include_fields' => ['field'],
                                    'controller' => ['name' => 'notDefault'],
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
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
            // Case #2. Chain inheritance.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                ],
                                'child' => [
                                    'parent' => 'parent',
                                    'include_fields' => ['field'],
                                    'controller' => ['name' => 'notDefault'],
                                ],
                                'grandchild' => [
                                    'parent' => 'child',
                                    'manager' => 'es.manager.test',
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v1.grandchild' => [
                        'parent' => 'child',
                        'manager' => 'es.manager.test',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
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
            // Case #3. Conflicting inheritance.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                    'include_fields' => ['field'],
                                ],
                                'child' => [
                                    'parent' => 'parent',
                                    'exclude_fields' => ['field2'],
                                    'controller' => [
                                        'name' => 'notDefault',
                                        'path' => '{param}',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => ['field2'],
                        'controller' => [
                            'name' => 'notDefault',
                            'path' => '/{param}',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                ],
            ],
            // Case #4. Chain inheritance with randomized order.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'greatGrandchild' => [
                                    'parent' => 'grandchild',
                                    'controller' => [
                                        'name' => 'notDefault2',
                                    ],
                                ],
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                ],
                                'grandchild' => [
                                    'parent' => 'child',
                                    'manager' => 'es.manager.test',
                                ],
                                'child' => [
                                    'parent' => 'parent',
                                    'include_fields' => ['field'],
                                    'controller' => [
                                        'name' => 'notDefault',
                                        'defaults' => [
                                            'param' => 'default',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [
                                'param' => 'default',
                            ],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v1.grandchild' => [
                        'parent' => 'child',
                        'manager' => 'es.manager.test',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [
                                'param' => 'default',
                            ],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v1.greatGrandchild' => [
                        'parent' => 'grandchild',
                        'manager' => 'es.manager.test',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault2',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                ],
            ],
            // Case #5. Override some fields.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                    'include_fields' => ['field'],
                                ],
                                'child' => [
                                    'parent' => 'parent',
                                    'controller' => ['name' => 'notDefault'],
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
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
            // Case #6. Complex tree.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'child3' => [
                                    'parent' => 'parent',
                                    'document' => 'AcmeTestBundle:ChildDocument',
                                ],
                                'greatGrandchild1_1_1' => [
                                    'parent' => 'grandchild1_1',
                                    'controller' => [
                                        'name' => 'notDefault2',
                                    ],
                                ],
                                'parent' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                ],
                                'grandchild1_1' => [
                                    'parent' => 'child1',
                                    'manager' => 'es.manager.test',
                                ],
                                'child1' => [
                                    'parent' => 'parent',
                                    'include_fields' => ['field'],
                                    'controller' => [
                                        'name' => 'notDefault',
                                        'defaults' => [
                                            'param' => 'default',
                                        ],
                                    ],
                                ],
                                'child2' => [
                                    'parent' => 'parent',
                                    'exclude_fields' => ['field'],
                                ],
                                'grandchild1_2' => [
                                    'parent' => 'child1',
                                    'exclude_fields' => ['field'],
                                ],
                                'grandchild2_2' => [
                                    'parent' => 'child2',
                                    'exclude_fields' => ['field2'],
                                ],
                            ],
                        ],
                    ],
                ],
                // Expected.
                [
                    'ongr_api.v1.parent' => [
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => [],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.child1' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [
                                'param' => 'default',
                            ],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v1.child2' => [
                        'parent' => 'parent',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => ['field'],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.grandchild1_1' => [
                        'parent' => 'child1',
                        'manager' => 'es.manager.test',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [
                                'param' => 'default',
                            ],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v1.grandchild1_2' => [
                        'parent' => 'child1',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => ['field'],
                        'controller' => [
                            'name' => 'notDefault',
                            'defaults' => [
                                'param' => 'default',
                            ],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                    'ongr_api.v1.grandchild2_2' => [
                        'parent' => 'child2',
                        'manager' => 'es.manager.default',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => [],
                        'exclude_fields' => ['field2'],
                        'controller' => ['name' => 'default'],
                    ],
                    'ongr_api.v1.greatGrandchild1_1_1' => [
                        'parent' => 'grandchild1_1',
                        'manager' => 'es.manager.test',
                        'document' => 'AcmeTestBundle:ParentDocument',
                        'include_fields' => ['field'],
                        'exclude_fields' => [],
                        'controller' => [
                            'name' => 'notDefault2',
                            'defaults' => [],
                            'requirements' => [],
                            'options' => [],
                            'params' => [],
                        ],
                    ],
                ],
            ],
            // Case #7. Include not array.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'endpoint' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                    'include_fields' => true,
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
                'include_fields should be array',
            ],
            // Case #8. Exclude not array.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'endpoint' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                    'exclude_fields' => 'wrong',
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
                'exclude_fields should be array',
            ],
            // Case #9. Include field not scalar.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'endpoint' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                    'include_fields' => ['field1', ['wrong']],
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
                'include_fields elements should scalar',
            ],
            // Case #10. Exclude field not scalar.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'endpoint' => [
                                    'manager' => 'es.manager.default',
                                    'document' => 'AcmeTestBundle:ParentDocument',
                                    'exclude_fields' => [['wrong'], 'field2'],
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
                'exclude_fields elements should scalar',
            ],
            // Case #11. Invalid manager.
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
            // Case #12. Invalid document.
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
            // Case #13. Invalid include exclude option.
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
            // Case #14. Parent to self.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'parent' => 'persons',
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
                'Endpoint \'persons\' can not be ancestor of itself.',
            ],
            // Case #15. Invalid parent.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'persons' => [
                                    'manager' => 'es.manager.default',
                                    'parent' => 'not_exist',
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
                'Invalid parent endpoint \'not_exist\'.',
            ],
            // Case #16. Cyclical inheritance.
            [
                // Config.
                [
                    'versions' => [
                        'v1' => [
                            'endpoints' => [
                                'endpoint0' => [
                                    'parent' => 'endpoint9',
                                ],
                                'endpoint1' => [
                                    'parent' => 'endpoint0',
                                ],
                                'endpoint2' => [
                                    'parent' => 'endpoint1',
                                ],
                                'endpoint3' => [
                                    'parent' => 'endpoint2',
                                ],
                                'endpoint4' => [
                                    'parent' => 'endpoint3',
                                ],
                                'endpoint5' => [
                                    'parent' => 'endpoint4',
                                ],
                                'endpoint6' => [
                                    'parent' => 'endpoint5',
                                ],
                                'endpoint7' => [
                                    'parent' => 'endpoint6',
                                ],
                                'endpoint8' => [
                                    'parent' => 'endpoint7',
                                ],
                                'endpoint9' => [
                                    'parent' => 'endpoint8',
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
                'Endpoint \'endpoint9\' can not be ancestor of itself.',
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
