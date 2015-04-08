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
     * @return ContainerBuilder
     */
    private function getDIContainer()
    {
        $container = new ContainerBuilder();

        return $container;
    }

    /**
     * Test whether exception is thrown when no manager is passed.
     */
    public function testInvalidEndpointManagerException()
    {
        $config = [
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
        ];

        $exceptionName = 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException';
        $this->setExpectedException($exceptionName);
        $this->setExpectedExceptionRegExp($exceptionName, '/^No manager set for endpoint \'persons\'.$/');
        self::$extension->load([$config], $this->getDIContainer());
    }

    /**
     * Test whether exception is thrown when no document is passed.
     */
    public function testInvalidEndpointDocumentException()
    {
        $config = [
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
        ];

        $exceptionName = 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException';
        $this->setExpectedException($exceptionName);
        $this->setExpectedExceptionRegExp($exceptionName, '/^No document set for endpoint \'persons\'.$/');
        self::$extension->load([$config], $this->getDIContainer());
    }

    /**
     * Test whether exception is thrown when include and exclude fields are set at the same time.
     */
    public function testInvalidIncludeExcludeException()
    {
        $config = [
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
        ];

        $exceptionName = 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException';
        $this->setExpectedException($exceptionName);
        $this->setExpectedExceptionRegExp(
            $exceptionName,
            '/^\'include_fields\' and \'exclude_fields\' can not be used together in endpoint \'persons\'.$/'
        );
        self::$extension->load([$config], $this->getDIContainer());
    }

    /**
     * Test whether exception is thrown when endpoint's parent is set to itself.
     */
    public function testParentToItselfException()
    {
        $config = [
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
        ];

        $exceptionName = 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException';
        $this->setExpectedException($exceptionName);
        $this->setExpectedExceptionRegExp(
            $exceptionName,
            '/^Endpoint \'persons\' can not be ancestor of itself.$/'
        );
        self::$extension->load([$config], $this->getDIContainer());
    }

    /**
     * Test whether exception is thrown when not existing parent is supplied.
     */
    public function testNotExistingParentException()
    {
        $config = [
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
        ];

        $exceptionName = 'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException';
        $this->setExpectedException($exceptionName);
        $this->setExpectedExceptionRegExp(
            $exceptionName,
            '/^Invalid parent endpoint \'not_exist\'.$/'
        );
        self::$extension->load([$config], $this->getDIContainer());
    }
}
