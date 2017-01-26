<?php

namespace Mmd\Account\Entity;

use Epos\SocialAuth\Entity\UserProfileTo;
use Epos\SocialAuth\View\Dto\SocialProviderTo;

/**
 * Class AttachedSocialProviderTo
 *
 * @package Mmd\Account\Entity
 */
class AttachedSocialProviderTo extends SocialProviderTo
{
    /**
     * @var UserProfileTo
     */
    protected $profile;

    /**
     * @var bool
     */
    protected $primary = false;

    /**
     * SocialProviderTo constructor.
     *
     * @param UserProfileTo $profile
     */
    public function __construct(UserProfileTo $profile)
    {
        $this->profile = $profile;
        parent::__construct($profile->getProvider());
    }

    /**
     * @return UserProfileTo
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return $this->primary;
    }

    /**
     * @param boolean $primary
     */
    public function setPrimary($primary = true)
    {
        $this->primary = (bool)$primary;
    }

}
