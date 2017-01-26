<?php

namespace Mmd\Account\Validator\Social;

use Epos\UserCore\Entity\UserInterface;
use Zend\Validator\ValidatorInterface;

/**
 * Interface ProfileValidatorInterface
 *
 * @package Mmd\Account\Validator\Social
 */
interface ProfileValidatorInterface extends ValidatorInterface
{
    public function setUser(UserInterface $user);
}
