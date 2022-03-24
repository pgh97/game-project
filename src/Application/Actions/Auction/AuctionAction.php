<?php

namespace App\Application\Actions\Auction;

use App\Application\Actions\Action;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\RedisService;
use App\Domain\Common\Service\ScribeService;
use App\Domain\User\Repository\UserRepository;
use Psr\Log\LoggerInterface;

abstract class AuctionAction extends Action
{
    protected AuctionRepository $auctionRepository;
    protected UserRepository $userRepository;
    protected CommonRepository $commonRepository;
    protected ScribeService $scribeService;
    protected RedisService $redisService;

    public function __construct(LoggerInterface $logger
        ,AuctionRepository $auctionRepository
        ,UserRepository $userRepository
        ,CommonRepository $commonRepository
        ,ScribeService $scribeService
        ,RedisService $redisService)
    {
        parent::__construct($logger);
        $this->auctionRepository = $auctionRepository;
        $this->userRepository = $userRepository;
        $this->commonRepository = $commonRepository;
        $this->scribeService = $scribeService;
        $this->redisService = $redisService;
    }
}