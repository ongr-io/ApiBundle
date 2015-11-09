<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ApiBundle\Validator;

use ONGR\ApiBundle\Request\RestRequest;
use ONGR\ElasticsearchBundle\Service\Repository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Validates document using symfony forms.
 */
class DocumentValidator implements ValidatorInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var bool
     */
    private $allowExtraFields = false;

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(RestRequest $restRequest)
    {
        $this->setRepository($restRequest->getRepository());
        $this->setAllowExtraFields($restRequest->isAllowedExtraFields());

        $data = $restRequest->getData();
        $this->getForm(true)->submit($data);

        return $this->getForm()->isValid() ? $data : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->getFormErrors($this->getForm());
    }

    /**
     * Collect form errors.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function getFormErrors(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                $childErrors = $this->getFormErrors($childForm);
                if ($childErrors) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    /**
     * @param bool $new
     *
     * @return FormInterface
     */
    protected function getForm($new = false)
    {
        if ($new) {
            unset($this->form);
        }

        if (!isset($this->form)) {
            $this->form = $this->getFormFactory()->create(
                'ongr_api_document_type',
                null,
                [
                    'metadata' => $this->getMetadata(),
                    'allow_extra_fields' => $this->isAllowingExtraFields(),
                ]
            );
        }

        return $this->form;
    }

    /**
     * @return DocumentTypeFactory
     */
    private function getTypeFactory()
    {
        return $this->typeFactory;
    }

    /**
     * @return FormFactoryInterface
     */
    private function getFormFactory()
    {
        return $this->formFactory;
    }

    /**
     * @return Repository
     */
    private function getMetadata()
    {
        $manager = $this->getRepository()->getManager();
        $types = $manager->getMetadataCollector()->getMappings($manager->getConfig()['mappings']);
        $repositoryTypes = $this->getRepository()->getTypes();

        return array_intersect_key($types, array_values($repositoryTypes));
    }

    /**
     * @return Repository
     */
    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param Repository $repository
     *
     * @return $this
     */
    protected function setRepository($repository)
    {
        $this->repository = $repository;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAllowingExtraFields()
    {
        return $this->allowExtraFields;
    }

    /**
     * @param bool $allowExtraFields
     */
    public function setAllowExtraFields($allowExtraFields)
    {
        $this->allowExtraFields = $allowExtraFields;
    }
}
