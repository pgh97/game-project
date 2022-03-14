<?php

namespace App\Domain\Shop\Service;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Common\Repository\CommonRepository;
use App\Domain\Common\Service\BaseService;
use App\Domain\Common\Service\RedisService;
use App\Domain\Shop\Entity\ShopInfoData;
use App\Domain\Shop\Repository\ShopRepository;
use Psr\Log\LoggerInterface;

class ShopService extends BaseService
{
    protected ShopRepository $shopRepository;
    protected CommonRepository $commonRepository;
    protected RedisService $redisService;
    protected LoggerInterface $logger;

    private const SHOP_REDIS_KEY = 'shop:%s';

    public function __construct(LoggerInterface $logger
        ,ShopRepository $shopRepository
        ,CommonRepository $commonRepository
        ,RedisService $redisService)
    {
        $this->logger = $logger;
        $this->shopRepository = $shopRepository;
        $this->commonRepository = $commonRepository;
        $this->redisService = $redisService;
    }

    public function getShopInfo(array $input):ShopInfoData
    {
        $data = json_decode((string) json_encode($input), false);
        $myShopInfo = new ShopInfoData();
        $myShopInfo->setShopCode($data->shopCode);

        $shopInfo = $this->shopRepository->getShopInfo($myShopInfo);
        $this->logger->info("get shop info service");
        return $shopInfo;
    }

    public function getShopInfoList(array $input):array
    {
        $data = json_decode((string) json_encode($input), false);
        $search = new SearchInfo();
        $search->setLimit($data->limit);
        $search->setOffset($data->offset);

        $shopArray = $this->shopRepository->getShopInfoList($search);
        $shopArrayCnt = $this->shopRepository->getShopInfoListCnt($search);
        $this->logger->info("get list shop info service");
        return [
            'shopList' => $shopArray,
            'totalCount' => $shopArrayCnt,
        ];
    }
}