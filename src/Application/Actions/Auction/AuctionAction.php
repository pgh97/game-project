<?php

namespace App\Application\Actions\Auction;

use App\Application\Actions\Action;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use Psr\Log\LoggerInterface;

abstract class AuctionAction extends Action
{
    protected AuctionRepository $auctionRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,AuctionRepository $auctionRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->auctionRepository = $auctionRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }
}