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

use Symfony\Component\Form\FormBuilderInterface;

trait ObjectResolverTrait
{
    /**
     * Resolves field and adds into form.
     *
     * @param FormBuilderInterface $builder
     * @param string               $name
     * @param array                $alias
     */
    public function resolveField(FormBuilderInterface $builder, $name, $alias)
    {
        if (in_array($alias['type'], ['object', 'nested'])) {
            $this->addObjectToForm($builder, $name, $alias);
        } else {
            $options = ['property_path' => $alias['propertyName']];

            if ($alias['type'] === 'date') {
                $alias['type'] = 'string';
            }

            $builder->add($name, $alias['type'], $options);
        }
    }

    /**
     * Resolves object and adds it to builder the right way.
     *
     * @param FormBuilderInterface $builder
     * @param string               $name
     * @param array                $alias
     */
    public function addObjectToForm(FormBuilderInterface $builder, $name, $alias)
    {
        $typeOptions = [
            'data_class' => $alias['namespace'],
            'types' => $alias['aliases'],
            'multiple' => (bool)$alias['multiple'],
        ];

        if ($typeOptions['multiple']) {
            $builder->add(
                $name,
                'collection',
                [
                    'type' => $alias['type'],
                    'allow_add' => true,
                    'cascade_validation' => true,
                    'property_path' => $alias['propertyName'],
                    'options' => $typeOptions,
                ]
            );
        } else {
            $typeOptions['property_path'] = $alias['propertyName'];
            $builder->add(
                $name,
                $alias['type'],
                $typeOptions
            );
        }
    }
}
