<?php

namespace App\Domain\Shop\Repository;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Shop\Entity\ShopInfoData;

interface ShopRepository
{
    /**
     * @param ShopInfoData $shopInfoData
     * @return ShopInfoData
     */
    public function getShopInfo(ShopInfoData $shopInfoData): ShopInfoData;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getShopInfoList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getShopInfoListCnt(SearchInfo $searchInfo): int;
}