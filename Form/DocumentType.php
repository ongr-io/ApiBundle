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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Populates form based on document metadata.
 */
class DocumentType extends AbstractType
{
    use ObjectResolverTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($options['metadata']->getAliases() as $name => $alias) {
            $this->resolveField($builder, $name, $alias);
        }

        foreach ($options['extra_fields'] as $name => $opt) {
            $builder->add($name, $opt['type']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                [
                    'csrf_protection' => false,
                    'extra_fields' => ['id' => ['type' => 'string']],
                ]
            )
            ->setRequired(['metadata'])
            ->setAllowedTypes(['metadata' => 'ONGR\ElasticsearchBundle\Mapping\ClassMetadata'])
            ->setDefault(
                'data_class',
                function (Options $options) {
                    return $options['metadata']->getNamespace();
                }
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ongr_api_document_type';
    }
}
