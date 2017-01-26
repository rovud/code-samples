<?php

namespace Mmd\Account\Validator\Social;

use Epos\SocialAuth\Entity\UserProfileTo;
use Epos\SocialAuth\Service\ProviderService;
use Epos\UserCore\Entity\UserInterface;
use InvalidArgumentException;
use RuntimeException;
use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class ProfileIsAvailable
 *
 * @package Mmd\Account\Validator\Social
 */
class ProfileIsAvailable extends AbstractValidator implements ProfileValidatorInterface
{

    const ERROR_NOT_BELONG = 'notBelong';

    protected $messageTemplates
        = [
            self::ERROR_NOT_BELONG => 'Социальный аккаунт привязан к другому профилю',
        ];

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var ProviderService
     */
    protected $providerService;

    /**
     * ProfileIsAvailable constructor.
     *
     * @param ProviderService $providerService
     * @param array           $options
     */
    public function __construct(ProviderService $providerService, array $options = [])
    {
        $this->providerService = $providerService;
        parent::__construct($options);
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  UserProfileTo $value
     *
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        $this->guardForUser();
        $this->guardForProfile($value);

        $userByEmail = $this->providerService->findByEmail($value->getEmail());
        if ($userByEmail && $userByEmail !== $this->user) {
            $this->error(static::ERROR_NOT_BELONG);

            return false;
        };

        $storedProvider = $this->providerService->findProviderForProfile($value);

        if (!$storedProvider) {
            return true;
        }

        if (!$storedProvider->getUser()->getId() !== $this->user->getId()) {
            $this->error(static::ERROR_NOT_BELONG);

            return false;
        }

        return true;
    }

    protected function guardForUser()
    {
        if (!isset($this->user)) {
            throw new RuntimeException(
                sprintf(
                    'Cannot proceed w/o User instance defined, define it via %s::%s', get_called_class(), 'setUser()'
                )
            );
        }
    }

    protected function guardForProfile($value)
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
    }
}
