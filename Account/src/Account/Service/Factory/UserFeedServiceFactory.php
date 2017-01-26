<?php

namespace Mmd\Account\Service\Factory;

use Interop\Container\ContainerInterface;
use Mmd\Account\Service\UserFeedService;
use Mmd\Auction\Dao\AuctionDaoInterface;
use Mmd\Event\Dao\RaidDaoInterface;
use Mmd\Guild\Service\MyGuildsService;

/**
 * Class UserFeedServiceFactory
 *
 * @package Mmd\Account\Service\Factory
 */
class UserFeedServiceFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $feedDao      = $container->get('guild.post.dao');
        $raidDao      = $container->get(RaidDaoInterface::class);
        $auctionDao   = $container->get(AuctionDaoInterface::class);
        $guildService = $container->get(MyGuildsService::class);

        $service = new UserFeedService($feedDao, $raidDao, $auctionDao, $guildService);

        return $service;
    }
}
