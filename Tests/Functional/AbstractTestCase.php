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

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ElasticsearchBundle\Command\IndexDropCommand;
use ONGR\ElasticsearchBundle\Command\IndexImportCommand;
use ONGR\ElasticsearchBundle\Command\IndexCreateCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Class for importing and removing data from ElasticSearch.
 */
abstract class AbstractTestCase extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        $output = new NullOutput();
        $command = new IndexCreateCommand();
        $command->setContainer(self::getContainer());
        $input = new ArrayInput([]);
        if ($command->run($input, $output) !== 0) {
            throw new \Exception('Failed to create index in ElasticSearch');
        }

        $command = new IndexImportCommand();
        $command->setContainer(self::getContainer());
        $input = new ArrayInput(
            [
                '--raw' => true,
                'filename' => __DIR__ . '/Fixtures/Bundles/Acme/TestBundle/Resources/data/persons.json',
            ]
        );
        if ($command->run($input, $output) !== 0) {
            throw new \Exception('Failed to import data to ElasticSearch');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        $output = new NullOutput();

        $command = new IndexDropCommand();
        $command->setContainer(self::getContainer());
        $input = new ArrayInput(['--force' => true]);
        if ($command->run($input, $output) !== 0) {
            throw new \Exception('Failed to drop index in ElasticSearch');
        }
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private static function getContainer()
    {
        return self::createClient()->getContainer();
    }
}
