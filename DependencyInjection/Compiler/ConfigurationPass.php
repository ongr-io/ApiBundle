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

use InvalidArgumentException;
use ONGR\ElasticsearchBundle\ORM\Manager;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ConfigurationPass.
 */
class ConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $versions = $container->getParameter('ongr_api.versions');
        foreach ($versions as $version) {
            $endpoints = $container->getParameter("ongr_api.{$version}.endpoints");
            foreach ($endpoints as $endpoint) {
                $this->validateEndpoint("ongr_api.{$version}.{$endpoint}", $container);
            }
        }
    }

    /**
     * Checks whether endpoint manager and document exists.
     *
     * @param string           $endpointName
     * @param ContainerBuilder $container
     *
     * @throws InvalidConfigurationException
     */
    private function validateEndpoint($endpointName, ContainerBuilder $container)
    {
        $endpoint = $container->getParameter($endpointName);

        try {
            /** @var Manager $manager */
            $manager = $container->get($endpoint['manager']);
        } catch (InvalidArgumentException $e) {
            throw new InvalidConfigurationException(
                "Non existing manager '{$endpoint['manager']}' provided for endpoint '{$endpointName}'",
                0,
                $e
            );
        }

        try {
            $manager->getRepository($endpoint['document']);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidConfigurationException(
                "Non existing document '{$endpoint['document']}' provided for endpoint '{$endpointName}'",
                0,
                $e
            );
        }
    }
}
