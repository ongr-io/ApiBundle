<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Unit\DependencyInjection\Compiler;

use ONGR\ApiBundle\DependencyInjection\Compiler\ConfigurationPass;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ConfigurationPassTest.
 */
class ConfigurationPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function setEndpoints($container, $config)
    {
        $versions = [];
        foreach ($config as $version => $endpoints) {
            $versions[] = $version;
            $versionEndpoints = [];
            foreach ($endpoints as $endpoint => $endpointConfig) {
                $versionEndpoints[] = $endpoint;
                $container->setParameter("ongr_api.{$version}.{$endpoint}", $endpointConfig);
            }
            $container->setParameter("ongr_api.{$version}.endpoints", $versionEndpoints);
        }
        $container->setParameter('ongr_api.versions', $versions);
    }

    /**
     * Data provider for validation test.
     *
     * @return array
     */
    public function validationTestProvider()
    {
        $cases = [];

        // Case #0. Valid manager and document.
        /** @var Repository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Repository');

        /** @var Manager|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()->getMock();
        $manager->expects($this->once())->method('getRepository')->with('validDocument')->willReturn($repository);

        $container = new ContainerBuilder();
        $container->set('validManager', $manager);
        $this->setEndpoints(
            $container,
            [
                'v1' => [
                    'endpoint' => [
                        'document' => 'validDocument',
                        'manager' => 'validManager',
                    ],
                ],
            ]
        );

        $cases[] = [$container];

        // Case #1. Invalid manager.
        $container = new ContainerBuilder();
        $this->setEndpoints(
            $container,
            [
                'v1' => [
                    'endpoint' => [
                        'manager' => 'invalidManager',
                    ],
                ],
            ]
        );

        $cases[] = [
            $container,
            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            "Non existing manager 'invalidManager' provided for endpoint 'ongr_api.v1.endpoint'",
        ];

        // Case #2. Invalid document.
        /** @var Manager|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMockBuilder('ONGR\ElasticsearchBundle\ORM\Manager')
            ->disableOriginalConstructor()->getMock();
        $manager->expects($this->once())
            ->method('getRepository')->with('invalidDocument')
            ->willThrowException(new \InvalidArgumentException());

        $container = new ContainerBuilder();
        $container->set('validManager', $manager);
        $this->setEndpoints(
            $container,
            [
                'v1' => [
                    'endpoint' => [
                        'document' => 'invalidDocument',
                        'manager' => 'validManager',
                    ],
                ],
            ]
        );

        $cases[] = [
            $container,
            'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException',
            "Non existing document 'invalidDocument' provided for endpoint 'ongr_api.v1.endpoint'",
        ];

        return $cases;
    }

    /**
     * Tests manager and document validation.
     *
     * @param ContainerBuilder $container
     * @param string           $expectedException
     * @param string           $expectedExceptionMessage
     *
     * @dataProvider validationTestProvider
     */
    public function testValidation($container, $expectedException = null, $expectedExceptionMessage = null)
    {
        $pass = new ConfigurationPass();

        if ($expectedException) {
            $this->setExpectedException($expectedException, $expectedExceptionMessage);
        }

        $pass->process($container);
    }
}
