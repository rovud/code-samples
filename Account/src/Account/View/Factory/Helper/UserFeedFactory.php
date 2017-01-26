<?php

namespace Mmd\Account\View\Factory\Helper;

use Epos\UserCore\Service\UserService;
use Interop\Container\ContainerInterface;
use Mmd\Account\Service\UserFeedService;
use Mmd\Account\View\Helper\UserFeed;
use Mmd\Util\ServiceLocator\ExtractServiceLocatorTrait;

/**
 * Class UserFeedFactory
 *
 * @package Mmd\Account\View\Factory\Helper
 */
class UserFeedFactory
{
    use ExtractServiceLocatorTrait;

    public function __invoke(ContainerInterface $container)
    {
        $sm = $this->extractServiceLocator($container);

        $userService     = $sm->get(UserService::class);
        $userFeedService = $sm->get(UserFeedService::class);

        return new UserFeed($userService, $userFeedService);
    }
}
