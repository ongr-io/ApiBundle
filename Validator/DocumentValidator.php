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

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

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
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * Validates data for submission.
     *
     * @param array $data
     *
     * @return bool
     */
    public function validate(array $data)
    {
        $this->getForm(true)->submit($data);

        return $this->getForm()->isValid();
    }

    /**
     * @return array
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
                if ($childErrors = $this->getFormErrors($childForm)) {
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
            $this->form = $this->formFactory->create('ongr_api_document_type');
        }

        return $this->form;
    }
}
