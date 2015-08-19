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

use ONGR\ElasticsearchBundle\Mapping\ClassMetadata;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DocumentType extends AbstractType
{
    use ObjectResolverTrait;

    /**
     * @var ClassMetadata
     */
    private $metadata;

    /**
     * @param ClassMetadata $repository
     */
    public function __construct(ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->getMetadata()->getAliases() as $name => $alias) {
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
                    'data_class' => $this->getMetadata()->getNamespace(),
                    'csrf_protection' => false,
                    'extra_fields' => ['id' => ['type' => 'string']],
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ongr_api_document_type';
    }

    /**
     * @return ClassMetadata
     */
    protected function getMetadata()
    {
        return $this->metadata;
    }
}
