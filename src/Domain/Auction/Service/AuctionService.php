<?php

namespace App\Domain\Auction\Service;

use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use Psr\Log\LoggerInterface;

class AuctionService extends BaseService
{
    protected AuctionRepository $auctionRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const AUCTION_REDIS_KEY = 'auction:%s';

    public function __construct(LoggerInterface $logger
        ,AuctionRepository $auctionRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->auctionRepository = $auctionRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}