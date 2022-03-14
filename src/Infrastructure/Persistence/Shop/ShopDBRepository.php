<?php

namespace App\Infrastructure\Persistence\Shop;

use App\Domain\Common\Entity\SearchInfo;
use App\Domain\Shop\Entity\ShopInfoData;
use App\Domain\Shop\Repository\ShopRepository;
use App\Infrastructure\Persistence\BaseRepository;

class ShopDBRepository extends BaseRepository implements ShopRepository
{
    public function getShopInfo(ShopInfoData $shopInfoData): ShopInfoData
    {
        $query = '
            SELECT 
                shop_code               AS shopCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,IF(sale_percent IS NULL, 0, sale_percent)           AS salePercent
                ,money_code             AS moneyCode
                ,item_price             AS itemPrice
                ,create_date            AS createDate
            FROM `shop_info_data`
            WHERE shop_code = :shopCode
        ';

        $statement = $this->database->prepare($query);
        $shopCode = $shopInfoData->getShopCode();

        $statement->bindParam(':shopCode', $shopCode);
        $statement->execute();

        return $statement->fetchObject(ShopInfoData::class);
    }

    public function getShopInfoList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                shop_code               AS shopCode
                ,item_code              AS itemCode
                ,item_type              AS itemType
                ,sale_percent           AS salePercent
                ,money_code             AS moneyCode
                ,item_price             AS itemPrice
                ,create_date            AS createDate
            FROM `shop_info_data`
            ORDER BY shop_code
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);
        $offset = $searchInfo->getOffset();
        $limit = $searchInfo->getLimit();

        $statement->bindParam(':offset', $offset);
        $statement->bindParam(':limit', $limit);
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function getShopInfoListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `shop_info_data`
        ';

        $statement = $this->database->prepare($query);
        $statement->execute();

        return $statement->fetchColumn();
    }
}