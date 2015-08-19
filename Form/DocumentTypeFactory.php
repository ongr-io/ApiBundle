<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Form;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DocumentTypeFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return DocumentType
     */
    public function get()
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->attributes->has('_route')
            && strpos($request->attributes->get('_route'), 'ongr_api_') !== false
        ) {
            return $this->build($request);
        }

        throw new \RuntimeException('Document type factory can only be called on ongr_api_ requests.');
    }

    /**
     * @param Request $request
     *
     * @return DocumentType
     */
    protected function build(Request $request)
    {
        $meta = $this
            ->container
            ->get($request->attributes->get('manager'))
            ->getBundlesMapping([$request->attributes->get('repository')]);

        return new DocumentType(reset($meta));
    }
}
