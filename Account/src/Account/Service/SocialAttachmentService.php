<?php

namespace Mmd\Account\Service;

use Epos\SocialAuth\Entity\UserProviderInterface;
use Epos\SocialAuth\Service\ProviderService;
use Epos\UserCore\Entity\UserInterface;
use Mmd\Account\Service\Exception\SocialAttachException;
use Mmd\Account\Validator\Social\ProfileValidatorInterface;
use OAuth\Common\Token\TokenInterface;

/**
 * Class SocialAttachmentService
 *
 * @package Mmd\Account\Service
 */
class SocialAttachmentService
{
    /**
     * @var ProviderService
     */
    protected $providerService;

    /**
     * @var ProfileValidatorInterface
     */
    protected $profileValidator;

    /**
     * SocialAttachmentService constructor.
     *
     * @param ProviderService           $providerService
     * @param ProfileValidatorInterface $profileValidator
     */
    public function __construct(ProviderService $providerService, ProfileValidatorInterface $profileValidator)
    {
        $this->providerService  = $providerService;
        $this->profileValidator = $profileValidator;
    }

    /**
     * @param UserInterface $user
     *
     * @return UserProviderInterface[]
     */
    public function findAttachedForUser(UserInterface $user)
    {
        return $this->providerService->findUserProviders($user);
    }

    /**
     * @param UserInterface  $user
     * @param string         $providerName
     * @param TokenInterface $token
     */
    public function attach(UserInterface $user, $providerName, TokenInterface $token)
    {
        try {
            $profile = $this->providerService->requestProfile($providerName);
        } catch (\Exception $ex) {
            throw SocialAttachException::profileIsNotAvailable($providerName, $user);
        }

        $this->profileValidator->setUser($user);
        if (!$this->profileValidator->isValid($profile)) {
            throw SocialAttachException::profileIsNotValid($profile, $this->profileValidator->getMessages());
        }

        $userProvider = $this->providerService->findProviderForProfile($profile);

        if (!$userProvider) {
            $this->providerService->createUserProvider($providerName, $profile, $user, $token);

            return;
        }

        $this->providerService->updateUserProvider($userProvider, $profile, $token);
    }

    /**
     * @param UserInterface $user
     * @param string        $providerName
     */
    public function detach(UserInterface $user, $providerName)
    {
        $matched       = null;
        $userProviders = $this->findAttachedForUser($user);

        foreach ($userProviders as $userProvider) {
            if (strcasecmp($userProvider->getProvider(), $providerName) === 0) {
                $matched = $userProvider;
                break;
            }
        }

        if (!$matched) {
            throw new \OutOfBoundsException(
                sprintf('Provider %s is not attached for user %u', $providerName, $user->getId())
            );
        }

        $this->providerService->removeUserProvider($matched);
    }
}
