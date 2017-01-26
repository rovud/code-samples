<?php

namespace Mmd\Account\Validator\Social;

use Epos\SocialAuth\Entity\UserProfileTo;
use Epos\UserCore\Entity\UserInterface;
use InvalidArgumentException;
use Zend\Validator\Exception;
use Zend\Validator\ValidatorChain;

/**
 * Class ProfileValidator
 *
 * @package Mmd\Account\Validator\Social
 */
class ProfileValidator extends ValidatorChain implements ProfileValidatorInterface
{

    public function setUser(UserInterface $user)
    {
        foreach ($this->validators as $validator) {
            $validator = $validator['instance'];
            if (!$validator instanceof ProfileValidatorInterface) {
                continue;
            }

            $validator->setUser($user);
        }
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     *
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value, $context = null)
    {
        if (!$value instanceof UserProfileTo) {
            throw new InvalidArgumentException(
                sprintf(
                    'Instance of %s expected, got %s',
                    UserProfileTo::class,
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return parent::isValid($value, $context);
    }
}
