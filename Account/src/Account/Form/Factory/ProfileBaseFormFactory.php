<?php

namespace Mmd\Account\Form\Factory;

use Interop\Container\ContainerInterface;
use Mmd\Account\Form\ProfileBaseForm;
use Mmd\Account\InputFilter\ProfileBaseFilter;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;
use Zend\Hydrator\ClassMethods;

/**
 * Class ProfileBaseFormFactory
 *
 * @package Mmd\Account\Form\Factory
 */
class ProfileBaseFormFactory
{

    use ExtractServiceLocatorTrait;

    public function __invoke(ContainerInterface $container)
    {
        $sm           = $this->extractServiceLocator($container);
        $inputFilters = $sm->get('InputFilterManager');
        $hydrators    = $sm->get('HydratorManager');

        $form = new ProfileBaseForm();
        $form->setInputFilter($inputFilters->get(ProfileBaseFilter::class));
        $form->setHydrator($hydrators->get(ClassMethods::class));

        return $form;
    }
}
