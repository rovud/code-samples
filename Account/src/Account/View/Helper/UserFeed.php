<?php

namespace Mmd\Account\View\Helper;

use Epos\UserCore\Service\UserService;
use Mmd\Account\Service\UserFeedService;
use Zend\View\Helper\AbstractHelper;

/**
 * Class UserFeed
 *
 * @package Mmd\Account\View\Helper
 */
class UserFeed extends AbstractHelper
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UserFeedService
     */
    private $userFeedService;

    /**
     * UserFeed constructor.
     *
     * @param UserService     $userService
     * @param UserFeedService $userFeedService
     */
    public function __construct(UserService $userService, UserFeedService $userFeedService)
    {
        $this->userService     = $userService;
        $this->userFeedService = $userFeedService;
    }

    public function __invoke()
    {
        return $this;
    }

    public function __toString()
    {
        return '';
    }

    public function guildFeed()
    {
        if (!$this->userService->hasAuthenticatedUser()) {
            return '';
        }

        $partial = $this->view->plugin('partial');
        $feed    = $this->userFeedService->news($this->userService->getAuthenticatedUser());

        return $partial('mmd-account/partial/guild-news-side-feed', ['feed' => $feed]);
    }

    public function raidsFeed()
    {
        if (!$this->userService->hasAuthenticatedUser()) {
            return '';
        }

        $partial = $this->view->plugin('partial');
        $feed    = $this->userFeedService->raids($this->userService->getAuthenticatedUser());

        return $partial('mmd-account/partial/guild-events-side-feed', ['feed' => $feed]);
    }

    public function auctionsFeed()
    {
        if (!$this->userService->hasAuthenticatedUser()) {
            return '';
        }

        $partial = $this->view->plugin('partial');
        $feed    = $this->userFeedService->auctions($this->userService->getAuthenticatedUser());

        return $partial('mmd-account/partial/guild-auctions-side-feed', ['feed' => $feed]);
    }
}
