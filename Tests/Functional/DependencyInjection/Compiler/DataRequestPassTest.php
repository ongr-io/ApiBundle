<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Tests for  DataRequestPass.
 */
class DataRequestPassTest extends WebTestCase
{
    /**
     * Check services are  created.
     */
    public function testService()
    {
        // First load up the default variables and check if they're set.
        $kernel = static::createClient()->getKernel();
        $container = $kernel->getContainer();
    }
}
