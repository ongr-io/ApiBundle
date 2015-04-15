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

use ONGR\ApiBundle\Event\PostGetEvent;
use ONGR\ApiBundle\Event\PreGetEvent;
use ONGR\ApiBundle\Service\DataRequestService;
use ONGR\ElasticsearchBundle\Test\AbstractElasticsearchTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests DataRequestService.
 */
class DataRequestServiceTest extends AbstractElasticsearchTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getDataArray()
    {
        return [
            'default' => [
                'person' => [
                    [
                        'name' => 'TestName1',
                        'surname' => 'TestSurname1',
                    ],
                    [
                        'name' => 'TestName2',
                        'surname' => 'TestSurname2',
                    ],
                    [
                        'name' => 'TestName3',
                        'surname' => 'TestSurname3',
                    ],
                ],
            ],
        ];
    }

    /**
     * Tests post get Event.
     */
    public function testPostGetEvent()
    {
        $request = new Request();
        $container = $this->getContainer();
        /** @var EventDispatcherInterface $dispatcher */
        $dispatcher = $container->get('event_dispatcher');
        $dispatcher->addListener(
            'ongr.api.test_endpoint.post.get',
            function (PostGetEvent $event) use ($request) {
                $this->assertEquals(
                    [
                        ['name' => 'TestName1'],
                        ['name' => 'TestName2'],
                        ['name' => 'TestName3'],
                    ],
                    $event->getResult()
                );
                $event->setResult(['override' => 'result']);
                $this->assertSame($request, $event->getRequest());
            }
        );

        $dispatcher->addListener(
            'ongr.api.test_endpoint.pre.get',
            function (PreGetEvent $event) use ($request) {
                $this->assertSame($request, $event->getRequest());
                $this->assertInstanceOf('ONGR\ElasticsearchBundle\ORM\Repository', $event->getRepository());
                $this->assertInstanceOf('ONGR\ElasticsearchBundle\DSL\Search', $event->getSearch());
            }
        );

        $service = new DataRequestService(
            $container,
            'es.manager',
            'AcmeTestBundle:PersonDocument',
            ['include_fields' => ['name']],
            'test_endpoint',
            $dispatcher
        );

        $result = $service->get($request);
        $this->assertEquals(['override' => 'result'], $result);
    }
}
