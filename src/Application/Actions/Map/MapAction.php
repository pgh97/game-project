<?php

namespace App\Application\Actions\Map;

use App\Application\Actions\Action;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\Map\Repository\MapRepository;
use App\Domain\Quest\Repository\QuestRepository;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class MapAction extends Action
{
    protected MapRepository $mapRepository;
    protected UserRepository $userRepository;
    protected AuctionRepository $auctionRepository;
    protected FishingRepository $fishingRepository;
    protected QuestRepository $questRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,MapRepository $mapRepository
        ,UserRepository $userRepository
        ,AuctionRepository $auctionRepository
        ,FishingRepository $fishingRepository
        ,QuestRepository $questRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->mapRepository = $mapRepository;
        $this->userRepository = $userRepository;
        $this->auctionRepository = $auctionRepository;
        $this->fishingRepository = $fishingRepository;
        $this->questRepository = $questRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}