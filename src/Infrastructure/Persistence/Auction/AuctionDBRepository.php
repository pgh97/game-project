<?php

namespace App\Infrastructure\Persistence\Auction;

use App\Domain\Auction\Entity\AuctionInfoData;
use App\Domain\Auction\Repository\AuctionRepository;
use App\Domain\Common\Entity\SearchInfo;
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
                ,create_date             AS createDate
            FROM `auction_info_data`
            WHERE user_code = :userCode AND auction_code = :auctionCode
        ';

        $statement = $this->database->prepare($query);
        $userCode = $auctionInfoData->getUserCode();
        $auctionCode = $auctionInfoData->getAuctionCode();

        $statement->bindParam(':userCode', $userCode);
        $statement->bindParam(':auctionCode', $auctionCode);
        $statement->execute();

        if($statement->fetchColumn() > 0){
            return $statement->fetchObject(AuctionInfoData::class);
        }else{
            $auctionInfo = new AuctionInfoData();
            $auctionInfo->setUserCode(0);
            return $auctionInfo;
        }
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
}