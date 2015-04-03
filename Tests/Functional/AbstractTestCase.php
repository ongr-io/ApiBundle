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

use ONGR\ElasticsearchBundle\Command\IndexDropCommand;
use ONGR\ElasticsearchBundle\Command\IndexImportCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use ONGR\ElasticsearchBundle\Command\IndexCreateCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class for importing and removing data from ElasticSearch.
 */
abstract class AbstractTestCase extends WebTestCase
{
    /**
     * @var Application
     */
    private static $application;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$application = new Application(self::createClient()->getKernel());

        self::$application->add(new IndexCreateCommand());
        $command = self::$application->find('es:index:create');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        self::$application->add(new IndexImportCommand());
        $command = self::$application->find('es:index:import');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                '--raw' => true,
                'filename' => __DIR__ . '/Fixtures/Bundles/Acme/TestBundle/Resources/data/persons.json',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        $indexDropCommand = new IndexDropCommand();
        $indexDropCommand->setContainer(self::getContainer());
        self::$application->add($indexDropCommand);

        $command = self::$application->find('es:index:drop');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                'command' => $command->getName(),
                '--force' => true,
            ]
        );
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private static function getContainer()
    {
        return self::createClient()->getContainer();
    }
}
