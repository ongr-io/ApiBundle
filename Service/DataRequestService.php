<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Service;

use ONGR\ApiBundle\Event\PostGetEvent;
use ONGR\ApiBundle\Event\PreGetEvent;
use ONGR\ElasticsearchBundle\DSL\Query\MatchAllQuery;
use ONGR\ElasticsearchBundle\DSL\Search;
use ONGR\ElasticsearchBundle\ORM\Manager;
use ONGR\ElasticsearchBundle\ORM\Repository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Manages requests to a Document repository.
 */
class DataRequestService
{
    const EVENT_PREFIX = 'ongr.api';
    const PRE_GET_EVENT_SUFFIX = 'pre.get';
    const POST_GET_EVENT_SUFFIX = 'post.get';

    /**
     * @var Manager Data Documents manager.
     */
    protected $dataManager;

    /**
     * @var Repository Data document's repository.
     */
    protected $dataRepository;

    /**
     * @var array Document's fields to include/exclude.
     */
    protected $fields;

    /**
     * @var string
     */
    private $document;

    /**
     * @var string
     */
    private $endpointName;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param ContainerInterface       $container
     * @param string                   $manager
     * @param string                   $document
     * @param array                    $fields
     * @param string                   $endpointName
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ContainerInterface $container,
        $manager,
        $document,
        $fields,
        $endpointName,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->document = $document;
        $this->dataManager = $container->get($manager);
        $this->dataRepository = $this->dataManager->getRepository($document);
        $this->fields = $fields;
        $this->endpointName = $endpointName;
        $this->setEventDispatcher($eventDispatcher);
    }

    /**
     * Repository getter.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function get($request)
    {
        /** @var Search $search */
        $search = $this->getDataRepository()->createSearch();

        $query = new MatchAllQuery();
        $search->addQuery($query);
        if (!empty($this->fields['include_fields'])) {
            $search->setFields($this->fields['include_fields']);
        } elseif (!empty($this->fields['exclude_fields'])) {
            $search->setSource(['exclude' => $this->fields['exclude_fields']]);
        }

        $this->dispatchPreGetEvent($request, $search, $this->dataRepository);
        $result = $this->getDataRepository()->execute($search, 'array');
        $result = $this->dispatchPostGetEvent($request, $result);

        return $result;
    }

    /**
     * @return Manager
     */
    public function getDataManager()
    {
        return $this->dataManager;
    }

    /**
     * @return Repository
     */
    public function getDataRepository()
    {
        return $this->dataRepository;
    }

    /**
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getEndpointName()
    {
        return $this->endpointName;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return $this
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;

        return $this;
    }

    /**
     * Dispatches preGetEvent.
     *
     * @param Request    $request
     * @param Search     $search
     * @param Repository $repository
     */
    protected function dispatchPreGetEvent(Request $request, Search $search, Repository $repository)
    {
        $eventName = $this->getEventName(self::PRE_GET_EVENT_SUFFIX);
        $event = new PreGetEvent($request, $search, $repository);
        $this->getEventDispatcher()->dispatch($eventName, $event);
    }

    /**
     * Dispatches postGetEvent.
     *
     * @param Request $request
     * @param array   $result
     *
     * @return array
     */
    protected function dispatchPostGetEvent(Request $request, $result)
    {
        $eventName = $this->getEventName(self::POST_GET_EVENT_SUFFIX);
        $event = new PostGetEvent($request, $result);
        $this->getEventDispatcher()->dispatch($eventName, $event);

        return $event->getResult();
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getEventName($name)
    {
        return self::EVENT_PREFIX . '.' . $this->getEndpointName() . '.' . $name;
    }
}
