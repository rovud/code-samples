<?php

namespace Mmd\Account\Form;

use Mmd\Account\InputFilter\ProfileBaseFilter;
use Zend\Form\Form as ZendForm;

/**
 * Class ProfileBaseForm
 *
 * @package Mmd\Account\Form
 */
class ProfileBaseForm extends ZendForm
{
    public function init()
    {
        $this->add(
            [
                'name'    => ProfileBaseFilter::EL_USERNAME,
                'type'    => 'Text',
                'options' => [
                    'label' => 'Имя пользователя',
                ],
            ]
        );

        $this->add(
            [
                'name'       => ProfileBaseFilter::EL_SUBMIT,
                'type'       => 'Button',
                'attributes' => [
                    'type' => 'submit',
                ],
                'options'    => [
                    'label'  => 'Сохранить',
                    'ignore' => true,
                ],
            ]
        );
    }
}
