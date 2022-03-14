<?php

namespace App\Domain\Auction\Service;

use App\Domain\Auction\Entity\AuctionInfoData;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Entity\SearchInfo;
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

    public function getAuctionInfo(array $input):AuctionInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myAuctionInfo = new AuctionInfoData();
        $myAuctionInfo->setUserCode($data->decoded->data->userCode);
        $myAuctionInfo->setAuctionCode($data->auctionCode);

        $auctionInfo = $this->auctionRepository->getAuctionInfo($myAuctionInfo);
        $this->logger->info("get auction info Action");
        return $auctionInfo;
    }

    public function getAuctionInfoList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $auctionArray = $this->auctionRepository->getAuctionInfoList($search);
        $auctionArrayCnt = $this->auctionRepository->getAuctionInfoListCnt($search);
        $this->logger->info("get list auction info Action");
        return [
            'auctionList' => $auctionArray,
            'totalCount' => $auctionArrayCnt,
        ];
    }
}