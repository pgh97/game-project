<?php

namespace App\Domain\Fishing\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Fishing\Repository\FishingRepository;
use App\Domain\User\Entity\UserFishInventoryInfo;
use Psr\Log\LoggerInterface;

class FishingService extends BaseService
{
    protected FishingRepository $fishingRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const FISHING_REDIS_KEY = 'fishing:%s';

    public function __construct(LoggerInterface $logger
        ,FishingRepository $fishingRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->fishingRepository = $fishingRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getFishInventory(array $input):UserFishInventoryInfo
    {
        $data = json_decode((string) json_encode($input), false);
        $myFishInventory = new UserFishInventoryInfo();
        $myFishInventory->setUserCode($data->decoded->data->accountCode);
        $myFishInventory->setFishInventoryCode($data->fishInventoryCode);

        $fishInventory = $this->fishingRepository->getUserFishInventory($myFishInventory);
        $this->logger->info("get fish inventory info service");
        return $fishInventory;
    }

    public function getFishInventoryList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setUserCode($data->decoded->data->userCode);
        $search->setItemCode($data->itemCode);
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $fishInventoryArray = $this->fishingRepository->getUserFishInventoryList($search);
        $fishInventoryArrayCnt = $this->fishingRepository->getUserFishInventoryListCnt($search);
        $this->logger->info("get list fish inventory info service");
        return [
            'fishInventoryList' => $fishInventoryArray,
            'totalCount' => $fishInventoryArrayCnt,
        ];
    }
}