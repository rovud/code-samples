<?php

namespace Mmd\Account\Form\Factory;

use Epos\UserCore\InputFilter\ChangePasswordFilter as BaseChangePasswordFilter;
use Interop\Container\ContainerInterface;
use Mmd\Account\Form\ChangePasswordForm;
use Mmd\Account\InputFilter\ChangePasswordFilter;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;

/**
 * Class ChangePasswordFormFactory
 *
 * @package Mmd\Account\Form\Factory
 */
class ChangePasswordFormFactory
{

    use ExtractServiceLocatorTrait;

    public function __invoke(ContainerInterface $container)
    {
        $sm = $this->extractServiceLocator($container);

        $filterManager = $sm->get('InputFilterManager');

        $form        = new ChangePasswordForm();
        $inputFilter = $filterManager->get(ChangePasswordFilter::class);
        $inputFilter->add(
            $filterManager->get(BaseChangePasswordFilter::class), ChangePasswordForm::FIELDSET_CHANGE_PASSWORD
        );
        $form->setInputFilter($inputFilter);

        return $form;
    }
}
