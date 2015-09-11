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

use Symfony\Component\Form\AbstractType;

/**
 * Nested type as object alias.
 */
class NestedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'object';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'nested';
    }
}
