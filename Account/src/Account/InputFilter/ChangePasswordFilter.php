<?php

namespace Mmd\Account\InputFilter;

use Epos\UserCore\Entity\User;
use Mmd\Account\Form\ChangePasswordForm;
use Mmd\Account\Validator\PasswordIsMatch;
use Zend\InputFilter\InputFilter as BaseFilter;
use Zend\Validator\NotEmpty;

/**
 * Class ChangePasswordFilter
 *
 * @package Mmd\Account\InputFilter
 */
class ChangePasswordFilter extends BaseFilter
{
    /**
     * This function is automatically called when creating element with factory. It
     * allows to perform various operations (add elements...)
     *
     * @return void
     */
    public function init()
    {
        $this->add(
            [
                'name'     => ChangePasswordForm::EL_USER_ID,
                'required' => true,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]
        );

        $this->add(
            [
                'name'             => ChangePasswordForm::EL_CURRENT_PASSWORD,
                'required'         => true,
                'break_on_failure' => true,
                'validators'       => [
                    [
                        'name'                   => NotEmpty::class,
                        'options'                => [
                            'message' => 'Не указан текущий пароль',
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name'    => PasswordIsMatch::class,
                        'options' => [
                            'object_repository' => User::class,
                            'identifier_field'  => ChangePasswordForm::EL_USER_ID,
                            'message'           => 'Текущий пароль указан неверно',
                        ],
                    ],
                ],
            ]
        );
    }

}
