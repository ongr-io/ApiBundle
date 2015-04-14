<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional\Service;

use ONGR\ApiBundle\Tests\Functional\AbstractApiDocumentationTestCase;

/**
 * Tests class NelmioExtractor.
 */
class NelmioExtractorTest extends AbstractApiDocumentationTestCase
{
    /**
     * Tests Documentation creation.
     */
    public function testAPIDoc()
    {
        $client = static::createClient();
        $response = $client->request('GET', '/_doc/');
        $return = $response->filter('.operation')->first()->filter('.content table')->text();
        $return = preg_replace('/\s+/', ' ', $return);
        $this->assertEquals('Parameter Type Versions Description name string * surname string * ', $return);
    }
}
