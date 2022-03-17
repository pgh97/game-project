<?php

namespace App\Domain\Auction\Repository;

use App\Domain\Auction\Entity\AuctionInfoData;
use App\Domain\Auction\Entity\AuctionRanking;
use App\Domain\Common\Entity\SearchInfo;
use App\Domain\User\Entity\UserFishInventoryInfo;

interface AuctionRepository
{
    /**
     * @param AuctionInfoData $auctionInfoData
     * @return AuctionInfoData
     */
    public function getAuctionInfo(AuctionInfoData $auctionInfoData): AuctionInfoData;

    /**
     * @param SearchInfo $searchInfo
     * @return array
     */
    public function getAuctionInfoList(SearchInfo $searchInfo): array;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function getAuctionInfoListCnt(SearchInfo $searchInfo): int;

    /**
     * @param UserFishInventoryInfo $userFishInventoryInfo
     * @return int
     */
    public function createAuctionInfo(UserFishInventoryInfo $userFishInventoryInfo): int;

    /**
     * @param AuctionInfoData $auctionInfoData
     * @return int
     */
    public function deleteAuctionInfo(AuctionInfoData $auctionInfoData): int;

    /**
     * @param SearchInfo $searchInfo
     * @return int
     */
    public function modifyAuctionInfoList(SearchInfo $searchInfo): int;

    /**
     * @param AuctionRanking $ranking
     * @return int
     */
    public function createAuctionRank(AuctionRanking $ranking): int;

    /**
     * @param AuctionRanking $ranking
     * @return int
     */
    public function getAuctionRankList(AuctionRanking $ranking): array;
}