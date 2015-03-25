<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class DataRequestService, for managing request to a Document repository.
 */
class DataRequestPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $versions = $container->getParameter('ongr_api.versions');

        foreach ($versions as $version) {
            $endpoints = $container->getParameter('ongr_api.' . $version . '.endpoints');

            foreach ($endpoints as $endpoint) {
                $service_name = 'ongr_api.' . $version . '.' . $endpoint . '.data_request';
            }
        }
    }
}
