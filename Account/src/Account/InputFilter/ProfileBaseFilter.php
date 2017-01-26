<?php

namespace Mmd\Account\InputFilter;

use Epos\UserCore\Entity\User;
use Zend\InputFilter\InputFilter as BaseFilter;

/**
 * Class ProfileBaseFilter
 *
 * @package Mmd\Account\InputFilter
 */
class ProfileBaseFilter extends BaseFilter
{

    const EL_ID       = 'id';
    const EL_USERNAME = 'username';
    const EL_SUBMIT   = 'profile_base_submit';

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
                'name'     => self::EL_ID,
                'required' => false,
                'filters'  => [
                    ['name' => 'Int'],
                ],
            ]
        );

        $this->add(
            [
                'name'       => self::EL_USERNAME,
                'required'   => true,
                'filters'    => [
                    ['name' => 'StringTrim'],
                    ['name' => 'StripTags'],
                ],
                'validators' => [
                    [
                        'name'                   => 'NotEmpty',
                        'options'                => [
                            'message' => 'Поле обязательно для заполнения',
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name'                   => 'StringLength',
                        'options'                => [
                            'min'     => 3,
                            'max'     => 255,
                            'message' => 'Имя пользователя должно быть от %min% до %max% символов',
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name'    => 'UniqueObject',
                        'options' => [
                            'object_repository' => User::class,
                            'fields'            => ['username'],
                            'use_context'       => true,
                            'message'           => 'Имя пользователя занято',
                        ],
                    ],
                ],
            ]
        );
    }

}
