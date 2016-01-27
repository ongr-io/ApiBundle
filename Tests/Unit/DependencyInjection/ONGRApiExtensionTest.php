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
use Symfony\Component\HttpFoundation\Request;

class ONGRApiExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function getData()
    {
        // Case 0. Tests if only repository is set.
        $config = [
            'ongr_api' => [
                'versions' => [
                    'v3' => [
                        'endpoints' => [
                            'persons' => [
                                'repository' => 'es.manager.default.person',
                                'methods' => [
                                    Request::METHOD_POST,
                                    Request::METHOD_GET,
                                ],
                                'allow_extra_fields' => false,
                                'allow_fields' => [],
                                'allow_get_all' => false,
                                'allow_batch' => true,
                                'variants' => false,
                                'batch' => true,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $out[] = [
            $config,
            $config['ongr_api']['versions'],
        ];

        // Case #1. Tests when there is no configuration.
        $config = [];
        $expected = [];
        $out[] = [
            $config,
            $expected,
        ];

        return $out;
    }

    /**
     * Check if load adds parameters to container as expected.
     *
     * @param array $parameters
     * @param array $expected
     *
     * @dataProvider getData
     */
    public function testLoad($parameters, $expected)
    {
        $container = $this->getLoadedExtension($parameters);

        $this->assertEquals(
            $expected,
            $container->getParameter('ongr_api.versions'),
            'Incorrect versions parameters.'
        );
    }

    /**
     * Tests if invalid configuration are checked.
     *
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testIncorrectConfiguration()
    {
        $parameters = [
            'ongr_api' => [
                'versions' => [
                    'v1' => [
                        'invalid' => 'value',
                    ],
                ],
            ],
        ];

        $this->getLoadedExtension($parameters);
    }

    /**
     * Loads API Extension to test various cases.
     *
     * @param array $parameters Configuration parameters.
     *
     * @return ContainerBuilder
     */
    private function getLoadedExtension(array $parameters)
    {
        $container = new ContainerBuilder();
        class_exists('mockClass') ?: eval('class mockClass {}');
        $container->setParameter('kernel.bundles', ['mockBundle' => 'mockClass']);
        $container->setParameter('kernel.cache_dir', '');
        $container->setParameter('kernel.debug', true);
        $extension = new ONGRApiExtension();
        $extension->load(
            $parameters,
            $container
        );

        return $container;
    }
}
