<?php

namespace App\Infrastructure\Persistence\Auction;

use App\Domain\Auction\Entity\AuctionInfoData;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserFishInventoryInfo;
use App\Infrastructure\Persistence\BaseRepository;

class AuctionDBRepository extends BaseRepository implements AuctionRepository
{

    public function getAuctionInfo(AuctionInfoData $auctionInfoData): AuctionInfoData
    {
        $query = '
           SELECT 
                auction_code               AS auctionCode
                ,user_code                 AS userCode
                ,fish_grade_code           AS fishGradeCode
                ,money_code                AS moneyCode
                ,auction_price             AS auctionPrice
                ,change_time               AS changeTime
                ,create_date               AS createDate
            FROM `auction_info_data`
            WHERE user_code = :userCode AND auction_code = :auctionCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $auctionInfoData->getUserCode();
        $auctionCode = $auctionInfoData->getAuctionCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':auctionCode', $auctionCode);
        $statement->execute();

        return $statement->fetchObject(AuctionInfoData::class);
    }

    public function getAuctionInfoList(SearchInfo $searchInfo): array
    {
        $query = '
            SELECT 
                auction_code               AS auctionCode
                ,user_code                 AS userCode
                ,fish_grade_code           AS fishGradeCode
                ,money_code                AS moneyCode
                ,auction_price             AS auctionPrice
                ,change_time               AS changeTime
                ,create_date             AS createDate
            FROM `auction_info_data`
            WHERE user_code = :userCode
            ORDER BY auction_code
            LIMIT :offset , :limit
        ';

        $statement = $this->database->prepare($query);

        $userCode = $searchInfo->getUserCode();
        $statement->bindParam(':userCode', $userCode);
        $offset = $searchInfo->getOffset();
        $statement->bindParam(':offset', $offset);
        $limit = $searchInfo->getLimit();
        $statement->bindParam(':limit', $limit);
        $statement->execute();

        return (array) $statement->fetchAll();
    }

    public function getAuctionInfoListCnt(SearchInfo $searchInfo): int
    {
        $query = '
            SELECT 
                COUNT(*)    AS count
            FROM `auction_info_data`
            WHERE user_code = :userCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $searchInfo->getUserCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->execute();

        return $statement->fetchColumn();
    }

    public function createAuctionInfo(UserFishInventoryInfo $userFishInventoryInfo): int
    {
        $query = '
                INSERT INTO `auction_info_data`
                    (`fish_grade_code`, `user_code`, `money_code`, `auction_price`, `change_time`, `create_date`)
                SELECT 
	                distinct(UI.fish_grade_code), UI.user_code, FG.money_code
                    , floor(FG.min_price + rand() * (FG.max_price-FG.min_price)), 6, NOW()
                FROM user_fish_inventory_info  UI
                JOIN fish_grade_data FG ON UI.fish_grade_code = FG.fish_grade_code
                WHERE UI.user_code=:userCode AND UI.map_code=:mapCode
            ';
        $statement = $this->database->prepare($query);
        $userCode = $userFishInventoryInfo->getUserCode();
        $mapCode = $userFishInventoryInfo->getMapCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':mapCode', $mapCode);
        $statement->execute();

        return (int) $this->database->lastInsertId();
    }
}