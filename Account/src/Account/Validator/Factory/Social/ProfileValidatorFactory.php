<?php

namespace Mmd\Account\Validator\Factory\Social;

use Interop\Container\ContainerInterface;
use Mmd\Account\Validator\Social\ProfileIsAvailable;
use Mmd\Account\Validator\Social\ProfileValidator;

/**
 * Class ProfileValidatorFactory
 *
 * @package Mmd\Account\Validator\Factory\Social
 */
class ProfileValidatorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $validator = new ProfileValidator();

        $validator->attach($container->get(ProfileIsAvailable::class));

        return $validator;
    }
}
