<?php

namespace Mmd\Account\Service;

use Epos\Dao\Criterion\Filter;
use Epos\UserCore\Entity\UserInterface;
use Mmd\Auction\Dao\AuctionDaoInterface;
use Mmd\Auction\Entity\Auction;
use Mmd\Event\Dao\RaidDaoInterface;
use Mmd\Event\Entity\Raid;
use Mmd\Feed\Dao\PostDaoInterface;
use Mmd\Feed\Entity\Post;
use Mmd\Guild\Entity\Guild;
use Mmd\Guild\Service\MyGuildsService;

/**
 * Class UserFeedService
 *
 * @package Mmd\Account\Service
 */
class UserFeedService
{

    /**
     * @var PostDaoInterface
     */
    private $feedDao;

    /**
     * @var RaidDaoInterface
     */
    private $raidDao;

    /**
     * @var AuctionDaoInterface
     */
    private $auctionDao;

    /**
     * @var MyGuildsService
     */
    private $guildService;

    /**
     * @var array
     */
    private $userGuilds = [];

    /**
     * UserFeedService constructor.
     *
     * @param PostDaoInterface    $feedDao
     * @param RaidDaoInterface    $raidDao
     * @param AuctionDaoInterface $auctionDao
     * @param MyGuildsService     $guildService
     */
    public function __construct(
        PostDaoInterface $feedDao,
        RaidDaoInterface $raidDao,
        AuctionDaoInterface $auctionDao,
        MyGuildsService $guildService
    ) {
        $this->feedDao      = $feedDao;
        $this->raidDao      = $raidDao;
        $this->auctionDao   = $auctionDao;
        $this->guildService = $guildService;
    }

    /**
     * @param UserInterface $user
     * @param int           $limit
     *
     * @return Post[]
     */
    public function news(UserInterface $user, $limit = 3)
    {
        $guilds = $this->findUserGuilds($user);
        $filter = new Filter();
        $filter->andConstraint('guild')->in(
            array_map(
                function (Guild $guild) {
                    return $guild->getId();
                }, $guilds
            )
        );
        $filter->orderBy('date', 'DESC');
        $filter->limit($limit);

        return $this->feedDao->findByFilter($filter);
    }

    /**
     * @param UserInterface $user
     * @param int           $limit
     *
     * @return Raid[]
     */
    public function raids(UserInterface $user, $limit = 3)
    {
        $guilds = $this->findUserGuilds($user);
        $filter = new Filter();
        $filter->andConstraint('guild')->in(
            array_map(
                function (Guild $guild) {
                    return $guild->getId();
                }, $guilds
            )
        );
        $filter->andConstraint('status')->eq(Raid::STATUS_OPEN);
        $filter->orderBy('date', 'DESC');
        $filter->limit($limit);

        return $this->raidDao->findByFilter($filter);
    }

    /**
     * @param UserInterface $user
     * @param int           $limit
     *
     * @return Auction[]
     */
    public function auctions(UserInterface $user, $limit = 3)
    {
        $guilds = $this->findUserGuilds($user);
        $filter = new Filter();
        $filter->andConstraint('guild')->in(
            array_map(
                function (Guild $guild) {
                    return $guild->getId();
                }, $guilds
            )
        );
        $filter->andConstraint('status')->eq(Auction::STATUS_OPEN);
        $filter->orderBy('date', 'ASC');
        $filter->limit($limit);

        return $this->auctionDao->findByFilter($filter);
    }

    /**
     * @param UserInterface $user
     *
     * @return Guild[]
     */
    private function findUserGuilds(UserInterface $user)
    {
        $userId = $user->getId();
        if (isset($this->userGuilds[$userId])) {
            return $this->userGuilds[$userId];
        }

        $guilds = $this->guildService->memberOfGuildsList($user);

        return $this->userGuilds[$userId] = empty($guilds) ? [] : $guilds;
    }
}
