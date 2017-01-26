<?php

namespace Mmd\Account\Form;

use Epos\UserCore\Form\ChangePasswordForm as BaseChangePasswordForm;
use Epos\UserCore\InputFilter\ChangePasswordFilter as BaseChangePasswordFilter;

/**
 * Class ChangePasswordForm
 *
 * @package Mmd\Account\Form
 */
class ChangePasswordForm extends BaseChangePasswordForm
{
    const EL_USER_ID          = 'id';
    const EL_CURRENT_PASSWORD = 'current-password';

    public function init()
    {
        $this->add(
            [
                'name' => static::EL_USER_ID,
                'type' => 'hidden',
            ]
        );
        $this->add(
            [
                'name'    => static::EL_CURRENT_PASSWORD,
                'type'    => 'Password',
                'options' => [
                    'label' => 'Текущий пароль',
                ],
            ]
        );

        parent::init();

        $this->get(BaseChangePasswordForm::FIELDSET_CHANGE_PASSWORD)
             ->get(BaseChangePasswordFilter::EL_PASSWORD)
             ->setLabel('Новый пароль');
        $this->get(static::EL_SUBMIT)->setLabel('Сохранить');
    }

    /**
     * Returns filtered new password value if any
     *
     * @return string
     */
    public function getPasswordValue()
    {
        $inputFilter = $this->getInputFilter()->get(BaseChangePasswordForm::FIELDSET_CHANGE_PASSWORD);

        return $inputFilter->getValue(BaseChangePasswordFilter::EL_PASSWORD);
    }

}
