<?php

namespace Mmd\Account\View\Helper;

use Epos\SocialAuth\Entity\UserProviderInterface;
use Epos\SocialAuth\Options\ProvidersOptions;
use Epos\SocialAuth\Provider\ProvidersMap;
use Epos\SocialAuth\Service\ProfileConverter;
use Epos\SocialAuth\View\Dto\SocialProviderTo;
use Epos\SocialAuth\View\Helper\SocialAuthenticate;
use Mmd\Account\Entity\AttachedSocialProviderTo;

/**
 * Class BindSocialProvider
 *
 * @package Mmd\Account\View\Helper
 */
class BindSocialProvider extends SocialAuthenticate
{

    /**
     * @var UserProviderInterface[]
     */
    protected $userProviders = [];

    /**
     * @var string
     */
    protected $attachedProviderTemplate = 'mmd-account/partial/attached-provider-item';

    /**
     * SocialAuthenticate constructor.
     *
     * @param ProvidersOptions $providersOptions
     */
    public function __construct(ProvidersOptions $providersOptions)
    {
        parent::__construct($providersOptions);
        $this->buttonTemplate = 'mmd-account/partial/bind-social-provider-button';
    }

    /**
     * @return UserProviderInterface[]
     */
    public function getUserProviders()
    {
        return $this->userProviders;
    }

    /**
     * @param UserProviderInterface[] $userProviders
     *
     * @return BindSocialProvider
     */
    public function setUserProviders($userProviders)
    {
        $this->userProviders = $userProviders;

        return $this;
    }

    /**
     * @param string $providerName
     *
     * @return string
     */
    protected function assembleUrl($providerName)
    {
        return $this->view->url('profile/social/attach', ['provider' => $providerName]);
    }

    /**
     * @param SocialProviderTo $provider
     *
     * @return string
     */
    protected function renderButton(SocialProviderTo $provider)
    {
        if ($userProvider = $this->findAttachedProvider($provider->getName())) {
            return $this->renderAttachedItem($this->createProviderTo($userProvider));
        }

        return parent::renderButton($provider);
    }

    /**
     * @param AttachedSocialProviderTo $provider
     *
     * @return string
     */
    protected function renderAttachedItem(AttachedSocialProviderTo $provider)
    {
        return $this->view->partial($this->attachedProviderTemplate, ['provider' => $provider]);
    }

    /**
     * @param string $providerName
     *
     * @return UserProviderInterface|null
     */
    protected function findAttachedProvider($providerName)
    {
        foreach ($this->userProviders as $userProvider) {
            if (strcasecmp($userProvider->getProvider(), $providerName) === 0) {
                return $userProvider;
            }
        }

        return null;
    }

    /**
     * @param UserProviderInterface $provider
     *
     * @return AttachedSocialProviderTo
     */
    private function createProviderTo(UserProviderInterface $provider)
    {
        $profile      = ProfileConverter::fromProviderEntity($provider);
        $providerName = $provider->getProvider();

        $socialProviderTo = new AttachedSocialProviderTo($profile);
        $socialProviderTo->setPrimary($provider->isPrimary());
        $socialProviderTo->setLabel(ProvidersMap::getLabel($providerName));
        $socialProviderTo->setClass(
            isset($this->classes[$providerName]) ? $this->classes[$providerName] : ''
        );
        $socialProviderTo->setUrl(
            $this->view->url('profile/social/detach', ['provider' => $providerName])
        );

        return $socialProviderTo;
    }

}
