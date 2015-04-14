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

use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Nelmio\ApiDocBundle\Extractor\HandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Route;

/**
 * Class NelmioExtractor.
 */
class NelmioExtractor implements HandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Parse route parameters in order to populate ApiDoc.
     *
     * @param ApiDoc            $annotation
     * @param array             $annotations
     * @param Route             $route
     * @param \ReflectionMethod $method
     */
    public function handle(ApiDoc $annotation, array $annotations, Route $route, \ReflectionMethod $method)
    {
        if (!is_string($route->getDefault('endpoint'))) {
            return;
        }
        /** @var DataRequestService $service */
        $service = $this->getContainer()->get($route->getDefault('endpoint'));
        $mapping = $service->getDataManager()->getBundlesMapping([$service->getDocument()]);
        $meta = $mapping[$service->getDocument()];

        $fields = $this->selectFields($meta->getProperties(), $service->getFields());
        $format = $this->getResponseFormat($fields);
        $annotation->setResponse($format);
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return $this
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Select correct fields from properties.
     *
     * @param array $properties
     * @param array $fields
     *
     * @return array
     */
    private function selectFields($properties, $fields)
    {
        if (!empty($fields['exclude_fields'])) {
            $properties = $this->excludeFields($properties, $fields['exclude_fields']);
        } elseif (!empty($fields['include_fields'])) {
            $properties = $this->includeFields($properties, $fields['include_fields']);
        }

        return $properties;
    }

    /**
     * Removes fields from properties.
     *
     * @param array $properties
     * @param array $fields
     *
     * @return mixed
     */
    private function excludeFields($properties, $fields)
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $properties)) {
                unset($properties[$field]);
            }
        }

        return $properties;
    }

    /**
     * Includes fields from properties.
     *
     * @param array $properties
     * @param array $fields
     *
     * @return mixed
     */
    private function includeFields($properties, $fields)
    {
        $ret = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $properties)) {
                $ret[$field] = $properties[$field];
            }
        }

        return $ret;
    }

    /**
     * Builds response format.
     *
     * @param array $fields
     *
     * @return array
     */
    private function getResponseFormat($fields)
    {
        $format = [];
        foreach ($fields as $name => $field) {
            $format[$name] = [
                'dataType' => $field['type'],
                'required' => false,
                'readonly' => false,
            ];
            if ($field['type'] == 'object') {
                $format[$name]['children'] = $this->getResponseFormat($field['properties']);
            }
        }

        return $format;
    }
}
