<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Functional;

use Doctrine\Common\Annotations\AnnotationRegistry;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;

/**
 * Loads ApiDoc annotations.
 */
class AbstractApiDocumentationTestCase extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        parent::setUp();
        AnnotationRegistry::registerFile(
            $this->getVendorDirectory() .
            '/nelmio/api-doc-bundle/Nelmio/ApiDocBundle/Annotation/ApiDoc.php'
        );
    }

    /**
     * Returns path to vendors.
     *
     * @return string
     */
    protected function getVendorDirectory()
    {
        // Going up 2 levels from current dir will give bundle root directory.
        $baseDir = dirname(dirname(__DIR__));
        if (basename(dirname(dirname($baseDir))) == 'vendor') {
            // If bundle is in vendors we need to remove ongr/api-bundle.
            return basename(dirname(dirname($baseDir)));
        } else {
            // Otherwise vendors should be in bundle root directory.
            return $baseDir . DIRECTORY_SEPARATOR . 'vendor';
        }
    }
}
