<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Tests\Unit\Service;

use ONGR\ApiBundle\Service\DataRequestService;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests DataRequestService.
 */
class DataRequestServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param null  $name
     * @param null  $document
     * @param array $fields
     *
     * @return DataRequestService
     */
    protected function getDataRequestService($name = null, $document = null, $fields = [])
    {
        /** @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject $container */
        $container = $this->getMockForAbstractClass('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->expects($this->any())->method('get')->willReturnArgument(0);

        /** @var Search|\PHPUnit_Framework_MockObject_MockObject $search */
        $search = $this->getMock('ONGR\ElasticsearchBundle\DSL\Search');
        /** @var Repository|\PHPUnit_Framework_MockObject_MockObject $repository */
        $repository = $this->getMock('ONGR\ElasticsearchBundle\ORM\Repository', [], [], '', false);
        $repository->expects($this->any())->method('createSearch')->willReturn($search);

        /** @var Manager|\PHPUnit_Framework_MockObject_MockObject $manager */
        $manager = $this->getMock('ONGR\ElasticsearchBundle\ORM\Manager', [], [], '', false);
        $manager->expects($this->any())->method('getRepository')->willReturn($repository);

        /** @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject $dispatcher */
        $dispatcher = $this->getMockForAbstractClass('Symfony\Component\EventDispatcher\EventDispatcherInterface');

        return new DataRequestService($container, $manager, $document, $fields, $name, $dispatcher);
    }

    /**
     * Tests whether pre and post get events are called.
     */
    public function testEvents()
    {
        $service = $this->getDataRequestService('endpoint');
        /** @var EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject $dispatcher */
        $dispatcher = $service->getEventDispatcher();
        $dispatcher->expects($this->exactly(2))->method('dispatch')->withConsecutive(
            [
                $this->equalTo('ongr.api.endpoint.pre.get'),
                $this->isInstanceOf('ONGR\ApiBundle\Event\PreGetEvent'),
            ],
            [
                $this->equalTo('ongr.api.endpoint.post.get'),
                $this->isInstanceOf('ONGR\ApiBundle\Event\PostGetEvent'),
            ]
        );

        /** @var Request|\PHPUnit_Framework_MockObject_MockObject $request */
        $request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $service->get($request);
    }
}
