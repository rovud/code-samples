<?php

namespace Mmd\Account\Service\Exception;

use Epos\SocialAuth\Entity\UserProfileTo;
use Epos\UserCore\Entity\UserInterface;
use Exception;
use Mmd\Util\Event\ValidationResult;
use RuntimeException;

/**
 * Class SocialAttachException
 *
 * @package Mmd\Account\Service\Exception
 */
class SocialAttachException extends RuntimeException
{
    const CODE_NOT_AVAILABLE = 1;
    const CODE_NOT_VALID     = 2;
    const CODE_UNDEFINED     = 500;

    /**
     * @var ValidationResult
     */
    protected $validationResult;

    /**
     * @param string        $providerName
     * @param UserInterface $user
     * @param Exception     $previous
     *
     * @return static
     */
    public static function profileIsNotAvailable($providerName, UserInterface $user, Exception $previous = null)
    {
        return new static(
            sprintf('User profile is not available for user %u :: provider %s', $user->getId(), $providerName),
            static::CODE_NOT_AVAILABLE,
            $previous
        );
    }

    /**
     * @param UserProfileTo $profile
     * @param array         $messages
     *
     * @return static
     */
    public static function profileIsNotValid(UserProfileTo $profile, array $messages)
    {
        $message = sprintf('Provider for %s::%s is not available', $profile->getProvider(), $profile->getUid());
        $e       = new static($message, static::CODE_NOT_VALID);

        $e->validationResult = new ValidationResult();
        $e->validationResult->validationChain(false)
                            ->setMessages($messages);

        return $e;
    }

    /**
     * @return ValidationResult
     */
    public function getValidationResult()
    {
        return $this->validationResult;
    }
}
