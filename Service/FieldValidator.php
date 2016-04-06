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

use ONGR\ElasticsearchBundle\Service\Repository;
use ONGR\ElasticsearchBundle\Service\Manager;
use Symfony\Component\HttpFoundation\Request;

class FieldValidator
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @var array
     */
    private $versions;

    /**
     * FieldValidator constructor.
     * @param Manager $manager
     * @param array $versions
     */
    public function __construct(Manager $manager, array $versions)
    {
        $this->manager = $manager;
        $this->versions = $versions;
    }

    /**
     * Validates fields if the allow_extra_fields property
     * is false or allow_fields are set
     *
     * @param Request    $request
     * @param Repository $repository
     * @param array      $data
     *
     * @return array
     */
    public function validateFields(Request $request, Repository $repository, $data)
    {
        $config = [];
        $validation = [];
        foreach ($this->versions as $version) {
            foreach ($version['endpoints'] as $endpoint) {
                if ($endpoint['repository'] == $request->attributes->get('repository')) {
                    $config = $endpoint;
                    break;
                }
            }
            if ($config != []) {
                break;
            }
        }
        if (!$config['allow_extra_fields'] || $config['allow_fields']) {
            $mapping = $this->manager->getMetadataCollector()->getMapping(
                $repository->getClassName()
            );
            $forbiddenFields = $mapping['properties'];
            if ($config['allow_fields']) {
                foreach ($config['allow_fields'] as $field) {
                    unset($forbiddenFields[$field]);
                }
            }
            foreach ($data as $parameter => $value) {
                if (!array_key_exists($parameter, $mapping['properties']) && $parameter != '_id') {
                    $validation['message'] = sprintf(
                        'Property `%s` does not exist in the mapping of `%s`.',
                        $parameter,
                        $repository->getType()
                    );
                    return $validation;
                }
                if ($config['allow_fields'] && array_key_exists($parameter, $forbiddenFields)) {
                    $validation['message'] = sprintf(
                        'You are not allowed to insert or modify the field `%s` in `%s`',
                        $parameter,
                        $repository->getType()
                    );
                    return $validation;
                }
            }
        }
        return $validation;
    }
}
